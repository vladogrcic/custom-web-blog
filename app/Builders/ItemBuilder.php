<?php
namespace App\Builders\ItemBuilder;

use App\Language;
use App\Tag;
use Auth;

class ItemBuilder
{
    private static $callingClass;
    private $orderBy;
    private $orderDir;
    private $foreignType;
    private $table;
    private $permission;
    private static $instance;

    public function __construct()
    {
        self::$callingClass = false;
        $this->orderBy = (string)'id';
        $this->orderDir = (string)'desc';
        $this->foreignType = (string)'';
        $this->permission = (bool)false;
        $this->filtered = (array)[];
    }
    public static function class(Object $value)
    {
        self::$instance = new self();
        self::$callingClass = $value;
        return self::$instance;
    }
    
    public function orderBy(string $value)
    {
        if (!$value) {
            $value='id';
        }
        $this->orderBy = $value;
        self::$instance = $this;
        return $this;
    }

    public function orderDir(string $value)
    {
        if (!$value) {
            $value='desc';
        }
        $this->orderDir = $value;
        self::$instance = $this;
        return $this;
    }
    public function foreignType(string $value)
    {
        if (!$value) {
            $value='';
        }
        $this->foreignType = $value;
        self::$instance = $this;
        return $this;
    }
    public function table(string $value)
    {
        if (!$value) {
            $value='';
        }
        $this->table = $value;
        self::$instance = $this;
        return $this;
    }
    public function permission(string $value)
    {
        if (!$value) {
            $value=false;
        }
        $this->permission = $value;
        self::$instance = $this;
        return $this;
    }
    
    public function processJson()
    {
        $orderBy = $this->orderBy;
        $orderDir = $this->orderDir;
        $foreignType = $this->foreignType;
        $permission = $this->permission;
        $class = self::$callingClass;
        if ($class) {
            $idColMax = $class::max('id');
            $filterByUser = Auth::user()->can($permission);
            if (!$filterByUser&&$permission) {
                $itemAll = ['items' => $class::where('author_id', '=', Auth::id())->orderBy($orderBy, $orderDir)->paginate(10), 'maxCol' => Tag::max('id')];
            } else {
                if($foreignType){
                    $itemAll = ['items' => $class::with(['users' => function ($q) {
                        $q->orderBy($foreignType, $orderDir);
                      }])->paginate(10), 'maxCol' => $class::max('id')];
                }
                else{
                    $itemAll = ['items' => $class::orderBy($orderBy, $orderDir)->paginate(10), 'maxCol' => $class::max('id')];
                }
            }
            foreach ($itemAll['items'] as $itemOne) {
                $itemsJSON[] = [
                    'id' => $itemOne->id,
                    'name' => $itemOne->name,
                    'slug' => $itemOne->slug,
                ];
                // if ($itemOne->language != null) {
                //     $itemOne->language->name;
                // }
            }
            return $itemsJSON;
        } else {
            return [];
        }
    }
    public function toJson()
    {
        return json_encode($this->processJson());
    }
    public function get()
    {
        $orderBy = $this->orderBy;
        $orderDir = $this->orderDir;
        $foreignType = $this->foreignType;
        $table = $this->table;
        $permission = $this->permission;
        $class = self::$callingClass;
        $tableName = (new $class)->getTable();
        $itemsJSON = ['items'=>[]];
        $itemsJSON += ['maxCol'=>''];
        $itemsJSON += ['pageCount'=>1];
        if ($class) {
            $idColMax = $class::max('id');
            $filterByUser = Auth::user()->can($permission);
            if (!$filterByUser&&$permission) {
                $itemAll = ['items' => $class::where('author_id', '=', Auth::id())->orderBy($orderBy, $orderDir)->paginate(10), 'maxCol' => $class::max('id')];
            } else {
                if($foreignType && $table){
                    $itemAll = ['items' => $class->join($table, $tableName.'.'.$orderBy, '=', $table.'.id')->select($tableName.'.*')->orderBy($table.'.'.$foreignType, $orderDir)->paginate(10), 'maxCol' => $class::max('id')];
                }
                else{
                    $itemAll = ['items' => $class::orderBy($orderBy, $orderDir)->paginate(10), 'maxCol' => $class::max('id')];
                }
            }
            foreach ($itemAll['items'] as $itemOne) {
                $itemsJSON['items'][] = $itemOne;
                if ($itemOne->language != null) {
                    $itemOne->language->name;
                }
            }
            
            $itemsJSON['maxCol'] = $class::max('id');
            $itemsJSON['pageCount'] = $itemAll['items']->lastPage();
            return ['itemsJSON' => $itemsJSON, 'itemAll' => $itemAll, 'idColMax' => $idColMax, 'pageCount' => $itemAll['items']->lastPage()];
        } else {
            return ['itemsJSON' => [], 'itemAll' => [], 'idColMax' => 0];
        }
    }
}
