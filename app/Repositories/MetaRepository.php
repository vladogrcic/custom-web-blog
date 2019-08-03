<?php
namespace App\Repositories;

use App\Post;
use App\Group;
use App\Tag;
use App\Media;
use App\User;
use Storage;
use App\Language;

/**
 * Repository for meta items like Profile and Settings page.
 *
 * @method void post(object $metaModel, Request $request) Updates or adds meta items to the database.
 *
 */
class MetaRepository
{
    public $table_column_meta_key;
    public $table_column_meta_value;
    public $user_id;
    public $metaModel;

    public function __construct()
    {
        $this->table_column_meta_key = 'meta_key';
        $this->table_column_meta_value = 'meta_value';
        $this->user_id = null;
    }
    
    /**
     * Updates or sets the meta.
     *
     * @param string $metaName Meta name.
     * @return void
     *
     */
    public function setMeta($metaName, $metaValue)
    {
        if ($this->user_id) {
            $meta = $this->metaModel::where('user_id', '=', $this->user_id)->where($this->table_column_meta_key, '=', $metaName)->first();
            if ($this->user_id) {
                $name->user_id = $this->user_id;
            }
        } else {
            $meta = $this->metaModel::where($this->table_column_meta_key, '=', $metaName)->first();
        }
        if ($meta && !$metaValue) {
            $meta->delete();
            return false;
        }
        if ($metaValue) {
            if ($meta) {
                $name = $this->metaModel::find($meta->id);
                $name[$this->table_column_meta_value] = $metaValue;
            } else {
                $name = $this->metaModel;
                if ($metaName&&$metaValue) {
                    $name[$this->table_column_meta_key] = $metaName;
                    $name[$this->table_column_meta_value] = $metaValue;
                }
            }
            $name->save();
        }
    }

    /**
     * Gets meta.
     *
     * @param string $metaName Meta name.
     * @return string
     *
     */
    public function getMeta($metaName)
    {
        if ($this->user_id) {
            $meta = $this->metaModel::where('user_id', '=', $this->user_id)->where($this->table_column_meta_key, '=', $metaName)->first();
        } else {
            $meta = $this->metaModel::where($this->table_column_meta_key, '=', $metaName)->first();
        }
        if ($meta) {
            return $meta->meta_value;
        } else {
            return false;
        }
    }
    
    /**
     * Deletes meta.
     *
     * @param string $metaName Meta name.
     * @return void
     *
     */
    public function unsetMeta($metaName)
    {
        if ($this->user_id) {
            $meta = $this->metaModel::where('user_id', '=', $this->user_id)->where($this->table_column_meta_key, '=', $metaName)->first();
        } else {
            $meta = $this->metaModel::where($this->table_column_meta_key, '=', $metaName)->first();
        }
        if ($meta) {
            $meta->delete();
        } else {
            return false;
        }
    }
}
