<?php
namespace App\Models;

use CodeIgniter\Model;

class WatchlistModel extends Model
{
    protected $table            = 'watchlist';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'movie_id'];
}