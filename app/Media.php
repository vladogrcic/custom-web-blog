<?php

namespace App;

use Illuminate\Http\Request;
use App\Media;
use App\User;

use Auth;
use Storage;
use Session;
use File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Helpers\General\GeneralHelper as General;
use \Waavi\Sanitizer\Sanitizer;

class Media
{
    /**
     * Convert bytes to a more usefull form.
     *
     * @param  string $file Url of the file that needs its size checked.
     * @return array Width and height outputed as array.
     */
    public static function getImageMetadata($file)
    {
        $configDisks = config('filesystems.disks.upload');

        list($width, $height, $type, $attr) = getimagesize(storage_path($configDisks['relroot'].'/').$file);
        $output = (object) array();
        $output->width = $width;
        $output->height = $height;
        return $output;
    }
    /**
     * Uploads file to a specific location. It also handles avatar images.
     *
     * @param  array $inFiles Files to be processed. Also some options that are needed for the files.
     * @param  string $urLink Location of the file consisting of folder names.
     * @param  array $inFolderLocs Gets the names of the config item names in order to get site and/or content url.
     * @return string Output filename.
     *
     */
    public function uploadFiles(array $inFiles=[], string $urLink=null, array $inFolderLocs = ['orig'=>'upload', 'thumbs'=>['thumb-360', 'thumb-640']])
    {
        $configDisks = config('filesystems.disks.'.$inFolderLocs['orig']);
        /**
         *
         * * Loop through the files received.
         *
         */
        for ($i=0; $i < count($inFiles); $i++) {
            if ($inFiles[$i]) {
                if (property_exists($inFiles[0], 'filename')) {
                    $filename = $inFiles[0]->filename;
                } else {
                    $filters = [
                        'filename'    =>  'trim|escape|strip_tags',
                    ];
                    $data = ['filename' => $inFiles[$i]->getClientOriginalName()];
                    $sanitizer  = new Sanitizer($data, $filters);
                    $filename = $sanitizer->sanitize()['filename'];
                    $path = $configDisks['root'].'/'.$urLink;
                    $filename = $this->check_file_exists($path, $filename);
                }

                if (property_exists($inFiles[0], 'square')) {
                    $square = $inFiles[0]->square;
                } else {
                    $square = false;
                }
                /**
                 *
                 * * Crop the image to the smallest original size of the image.
                 * * If not defined it just saves the files to location.
                 * * Meant mostly for the user avatar.
                 *
                 */
                if ($square) {
                    $size_orig_all = getimagesize($inFiles[$i]);
                    $size_orig = min([$size_orig_all[0], $size_orig_all[1]]);
                    $image_orig = Image::make($inFiles[$i]->getRealPath());
                    $image_orig->crop($size_orig, $size_orig);

                    if (property_exists($inFiles[0], 'customSize')) {
                        $customSize = $inFiles[0]->customSize;
                        $image_orig->resize($customSize, $customSize);
                    }
                    
                    $image_orig->resize($size_orig, $size_orig);
                    $image_orig->encode('jpg', 75);
                    /**
                     *
                     * * Creating folder for the original image if non-existent.
                     *
                     */
                    Storage::disk($inFolderLocs['orig'])->makeDirectory('');
                    $image_orig->save($configDisks['root'].'/'.$filename);
                    $output = $filename;
                } else {
                    if ($urLink) {
                        $urLink = '/'.$urLink;
                    }
                    Storage::disk($inFolderLocs['orig'])->put($urLink.'/'.$filename, File::get($inFiles[$i]));
                    $output = $filename;
                }
                /**
                 *
                 * * Handling thumbnails for images.
                 *
                 */
                if(array_key_exists('thumbs', $inFolderLocs)){
                    for ($j=0; $j < count($inFolderLocs['thumbs']); $j++) {
                        $loc = $inFolderLocs['thumbs'][$j];
                        $configDisksThumbs = config('filesystems.disks.'.$loc);
            
                        $image_thumb = $inFiles[$i];
                        $size_thumb = explode("-", $loc);
                        $image_resize = Image::make($image_thumb->getRealPath());
                        $size_orig_all = getimagesize($inFiles[$i]);
                        $size_orig = min([$size_orig_all[0], $size_orig_all[1]]);
                        /**
                         *
                         * * Crop the image to the smallest original size of the image.
                         *
                         */
                        if ($square) {
                            $image_resize->crop($size_orig, $size_orig);
                        }
            
                        $image_resize->resize($size_thumb[1], null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $folderTree = explode("/", $urLink);
                        /**
                         *
                         * * Creating folders in thumbs if non-existent.
                         *
                         */
                        for ($k=0; $k < count($folderTree); $k++) {
                            Storage::disk($loc)->makeDirectory($folderTree[$k]);
                        }
                        $folderTree = implode("/", $folderTree);
            
                        $image_resize->save($configDisksThumbs['root'].($folderTree.'/'.$filename));
                    }
                }
            }
        }
        return $output;
    }
    /**
     *
     * @param  array $inFiles Files to be processed. Also some options that are needed for the files.
     * @return string Output calculated size of the image.
     *
     */
    public function calcSquareImageSize(array $inFiles = [])
    {
        $size_all = getimagesize($inFiles);
        $size = min([$size_all[0], $size_all[1]]);
        return $size;
    }
    
    /**
     * Get the size of the folder.
     *
     * @param  string $urLink Folder that needs the size checked.
     * @return string Final pattern og the folder size. Example "0 B"
     */
    public static function getFolderSize($urLink)
    {
        $files_with_size = array();
        $files = Storage::allFiles($urLink);
        $folderSize = 0;
        foreach ($files as $key => $file) {
            $files_with_size[$key]['name'] = $file;
            $files_with_size[$key]['size'] = Storage::size($file);
            $folderSize += Storage::size($file);
        }
        return General::bytesToHuman($folderSize);
    }
    /**
     * Get list of folders and their props.
     *
     * @param  string $urLink Folder you want to get its subfolders or their props from
     * @return array List of folders and their props.
     */
    public static function outputFolderList($urLink, $uploadFolder='upload')
    {
        $output['files'] = Storage::disk($uploadFolder)->files($urLink);
        $output['allFiles'] = Storage::disk($uploadFolder)->allFiles('/');
        $output['folders'] = Storage::disk($uploadFolder)->directories($urLink);
        
        $output['folderTree'] = explode("/", $urLink);
        $output['url'] = $urLink;
        for ($i=0; $i < count($output['folders']); $i++) {
            $folder = ''.$output['folders'][$i];
            $output['folderProps'][$i]['url'] = Storage::url($output['folders'][$i]);
            $output['folderProps'][$i]['number'] = count(Storage::disk($uploadFolder)->directories($folder));
            $output['folderProps'][$i]['files'] = count(Storage::disk($uploadFolder)->files($folder));
            $output['folderProps'][$i]['name'] = basename($folder);

            $output['folderProps'][$i]['size'] = self::getFolderSize(($folder));
            $output['folderProps'][$i]['modified']['date'] = gmdate("d.m.Y", Storage::disk($uploadFolder)->lastModified($folder)+3600);
            $output['folderProps'][$i]['modified']['time'] = gmdate("H:i:s", Storage::disk($uploadFolder)->lastModified($folder)+3600);
        }
        for ($i=0; $i < count($output['files']); $i++) {
            $output['fileProps'][$i] = self::outputFileProps($output['files'][$i]);
        }
        return $output;
    }
    /**
     * Get file props.
     *
     * @param  string $urLink Folder you want to get its files or their props from.
     * @return array List of file props.
     */
    public static function outputFileProps($urLink)
    {
        $configDisks = config('filesystems.disks.upload');
        $configDisks_thumb640 = config('filesystems.disks.thumb-640');
        $file = ''.$urLink;
        $output['name'] = basename($file);
        $output['coreUrl'] = $file;
        $output['url'] = Storage::disk('upload')->url($configDisks['storageUrl'].'/'.$file);
        $output['thumbUrl'] = Storage::disk('thumb-640')->url($configDisks_thumb640['storageUrl'].'/'.$file);
        $output['mimeType'] = Storage::disk('upload')->mimeType($file);
        $output['resolution'] = ['width'=> self::getImageMetadata($file)->width, 'height' => self::getImageMetadata($file)->height];
        $output['modified']['date'] = gmdate("d.m.Y", Storage::disk('upload')->lastModified($file)+3600);
        $output['modified']['time'] = gmdate("H:i:s", Storage::disk('upload')->lastModified($file)+3600);
        $output['size'] = General::bytesToHuman(Storage::disk('upload')->size($file));
        $output['exif'] = Image::make(storage_path($configDisks['relroot'].'/').$file)->exif();
        if ($output['exif']['COMPUTED']) {
            $output['exif']['COMPUTED'] = General::convert_from_latin1_to_utf8_recursively($output['exif']['COMPUTED']);
        }
        if ($output['exif']) {
            if (array_key_exists("DateTimeOriginal", $output['exif'])) {
                $time = $output['exif']['DateTimeOriginal'];
                $output['exif']['DateTimeOriginal'] = ['date' => date('d.m.Y', strtotime($time))];
                $output['exif']['DateTimeOriginal'] += ['time' => date('H:i:s', strtotime($time))];
            }
        } else {
            $time = "2017:11:25 13:59:21";
            $output['exif']['DateTimeOriginal'] = ['date' => date('d.m.Y', strtotime($time))];
            $output['exif']['DateTimeOriginal'] += ['time' => date('H:i:s', strtotime($time))];
        }
        return General::convert_from_latin1_to_utf8_recursively($output);
    }
    /**
     * Check if file exists. If it does add an number increment.
     *
     * @param  string $path Path to file.
     * @param  string $filename File name to check.
     * @return string New filename which doesn't correspond to an existing one..
     */
    public function check_file_exists($path, $filename)
    {
        if ($pos = strrpos($filename, '.')) {
            $name = substr($filename, 0, $pos);
            $ext = substr($filename, $pos);
        } else {
            $name = $filename;
        }
        $newpath = $path.'/'.$filename;
        $newname = $filename;
        $counter = 0;
        while (file_exists($newpath)) {
            $newname = $name .'_'. $counter . $ext;
            $newpath = $path.'/'.$newname;
            $counter++;
        }
        return $newname;
    }
}
