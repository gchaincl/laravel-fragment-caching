<?php namespace Gchaincl\LaravelFragmentCaching;

class Factory extends \Illuminate\View\Factory {

    public function cacheif($condition, $key, \Closure $closure)
    {
        if ( ! $condition ) {
            return $closure();
        }
        $cache = $this->getContainer()['cache'];
        $log = $this->getContainer()['log'];

        $content = $cache->get($key);
        if ( ! $content ) {
            ob_start();

            $closure();
            $content = ob_get_contents();
            ob_end_clean();
            $cache->forever($key, $content);
            $log->debug('writing cache', [$key]);
        } else {
            $log->debug('reading cache', [$key]);
        }

        return $content;
    }

    public function cache($key, \Closure $closure)
    {
        return $this->cacheif(true, $key, $closure);
    }

}
