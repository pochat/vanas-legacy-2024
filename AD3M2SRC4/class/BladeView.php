<?php
require_once PATH_ADM_VENDOR . '/autoload.php';
//use Philo\Blade\Blade;
class BladeView extends Philo\Blade\Blade{
    public function __construct($views = PATH_ADM_VIEWS, $cache = PATH_ADM_CACHE) {
        parent::__construct($views, $cache);
    }
    
}
