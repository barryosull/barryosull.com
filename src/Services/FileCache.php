<?php namespace Nekudo\ShinyBlog\Services;

use Psr\SimpleCache\CacheInterface;

class FileCache implements CacheInterface
{
    private $file_path;

    public function __construct()
    {
        $cache_path = __DIR__."/../../storage/cache";
        $this->file_path = $cache_path;
        $this->createCacheFolder();
    }

    private function createCacheFolder()
    {
        if (!file_exists($this->file_path)){
            mkdir($this->file_path);
        }
    }

    public function set($key, $value, $ttl = null)
    {
        $key = $this->cleanKey($key);
        $serialised = serialize($value);
        $file_path = $this->file_path."/".$key;
        file_put_contents($file_path, $serialised);
    }

    public function get($key, $default = null)
    {
        $key = $this->cleanKey($key);
        $file_path = $this->file_path."/".$key;
        if (!file_exists($file_path)) {
            return $default;
        }
        return unserialize(file_get_contents($file_path));
    }

    public function delete($key)
    {
        $key = $this->cleanKey($key);
        $file_path = $this->file_path."/".$key;
        `rm $file_path`;
    }

    private function cleanKey($key): string
    {
        return str_replace(" ", "", strtolower($key));
    }

    public function clear()
    {
        `rm -rf $this->file_path`;
        $this->createCacheFolder();
    }

    public function getMultiple($keys, $default = null)
    {
       return array_map(function($key) use ($default) {
           return $this->get($key, $default);
       }, $keys);
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key=>$value) {
            $this->set($key, $value);
        }
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function has($key)
    {
        $key = $this->cleanKey($key);
        $file_path = $this->file_path."/".$key;
        return file_exists($file_path);
    }
}