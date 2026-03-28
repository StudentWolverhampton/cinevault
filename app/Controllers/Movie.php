<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ReviewModel;
use App\Models\WatchlistModel;

class Movie extends Controller
{
    private string $apiKey = '65005ca1615a9ab654bb466a14087ab1';

    // ─── HOME ─────────────────────────────────────────────────────
    public function index()
    {
        $movies = $this->getTrending();
        return view('home', ['movies' => $movies]);
    }

    // ─── SEARCH (AJAX) ────────────────────────────────────────────
    public function search()
    {
        $query = $this->request->getGet('q');
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON([]);
        }

        $url = "https://api.themoviedb.org/3/search/movie?api_key={$this->apiKey}&query=" . urlencode($query);
        $response = @file_get_contents($url);
        if ($response === false) return $this->response->setJSON([]);

        $data = json_decode($response, true);
        return $this->response->setJSON($data['results'] ?? []);
    }

    // ─── MOVIE DETAIL ─────────────────────────────────────────────
    public function detail($id = null)
    {
        if (!$id) return redirect()->to('/');

        $url = "https://api.themoviedb.org/3/movie/{$id}?api_key={$this->apiKey}&language=en-US&append_to_response=credits,videos";
        $response = @file_get_contents($url);
        if ($response === false) return redirect()->to('/');

        $movie = json_decode($response, true);

        // Get reviews joined with username
        $db = \Config\Database::connect();
        $reviews = $db->table('reviews')
                      ->select('reviews.*, users.username')
                      ->join('users', 'users.id = reviews.user_id', 'left')
                      ->where('reviews.movie_id', $id)
                      ->orderBy('reviews.created_at', 'DESC')
                      ->get()
                      ->getResultArray();

        // Check if current user has this in their watchlist
        $inWatchlist = false;
        if (session()->get('is_logged_in')) {
            $watchlistModel = new WatchlistModel();
            $inWatchlist = $watchlistModel
                ->where('user_id', session()->get('user_id'))
                ->where('movie_id', $id)
                ->first() !== null;
        }

        return view('movie_detail', [
            'movie'       => $movie,
            'reviews'     => $reviews,
            'inWatchlist' => $inWatchlist,
        ]);
    }

    // ─── ADD REVIEW (AJAX) ────────────────────────────────────────
    public function addReview()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false]);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login to leave a review.']);
        }

        $movieId = $this->request->getPost('movie_id');
        $rating  = $this->request->getPost('rating');
        $comment = trim($this->request->getPost('comment') ?? '');

        if (!$movieId || !$rating || !$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        $reviewModel = new ReviewModel();
        $data = [
            'user_id'  => $userId,
            'movie_id' => $movieId,
            'rating'   => (int) $rating,
            'comment'  => $comment,
        ];

        if ($reviewModel->insert($data)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Could not save review.']);
    }

    // ─── TOGGLE WATCHLIST (AJAX) ──────────────────────────────────
    public function toggleWatchlist()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false]);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please login first.']);
        }

        $movieId = $this->request->getPost('movie_id');
        if (!$movieId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid movie.']);
        }

        $watchlistModel = new WatchlistModel();
        $existing = $watchlistModel
            ->where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

        if ($existing) {
            $watchlistModel->where('user_id', $userId)->where('movie_id', $movieId)->delete();
            return $this->response->setJSON(['success' => true, 'action' => 'removed']);
        } else {
            $watchlistModel->insert(['user_id' => $userId, 'movie_id' => $movieId]);
            return $this->response->setJSON(['success' => true, 'action' => 'added']);
        }
    }

    // ─── WATCHLIST PAGE ───────────────────────────────────────────
    public function watchlist()
    {
        if (!session()->get('is_logged_in')) {
            session()->setFlashdata('error', 'Please login to view your watchlist.');
            return redirect()->to('/user/login');
        }

        $userId = session()->get('user_id');
        $db = \Config\Database::connect();
        $watchlistItems = $db->table('watchlist')
                             ->where('user_id', $userId)
                             ->orderBy('created_at', 'DESC')
                             ->get()
                             ->getResultArray();

        // Fetch movie details from TMDB for each item
        $movies = [];
        foreach ($watchlistItems as $item) {
            $url = "https://api.themoviedb.org/3/movie/{$item['movie_id']}?api_key={$this->apiKey}&language=en-US";
            $response = @file_get_contents($url);
            if ($response !== false) {
                $movie = json_decode($response, true);
                $movie['movie_id'] = $item['movie_id'];
                $movies[] = $movie;
            }
        }

        return view('watchlist', ['movies' => $movies]);
    }

    // ─── PRIVATE HELPERS ──────────────────────────────────────────
    private function getTrending(): array
    {
        $url = "https://api.themoviedb.org/3/trending/movie/week?api_key={$this->apiKey}";
        $response = @file_get_contents($url);
        if ($response === false) return [];
        $data = json_decode($response, true);
        return $data['results'] ?? [];
    }
}
