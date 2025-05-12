<?php
require_once '../../model/Stream.php';
require_once '../../model/Video.php';

class StreamController {
    private $streamModel;
    private $videoModel;

    public function __construct() {
        $this->streamModel = new Stream();
        $this->videoModel = new Video();
    }

    public function index() {
        $sort = $_GET['sort'] ?? 'title_asc';
        $validSorts = [
            'title_asc' => 'title ASC',
            'title_desc' => 'title DESC',
            'newest' => 'id DESC',
            'oldest' => 'id ASC'
        ];
        $orderBy = $validSorts[$sort] ?? 'title ASC';

        $searchQuery = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
        if ($searchQuery) {
            $streams = $this->streamModel->searchStreams($searchQuery, $orderBy);
            $videos = $this->videoModel->searchVideos($searchQuery, $orderBy);
        } else {
            $streams = $this->streamModel->getAll($orderBy);
            $videos = $this->videoModel->getAllVideos($orderBy);
        }

        return compact('streams', 'videos');
    }
}
?>