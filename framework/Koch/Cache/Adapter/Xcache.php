<?php

/**
 * Koch Framework
 * Jens-Andr� Koch � 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;

/**
 * Koch Framework - Cache Handler for Xcache.
 *
 * XCache is a open-source opcode cacher, which means that it accelerates the performance of PHP on servers.
 * It optimizes performance by removing the compilation time of PHP scripts by caching the compiled state of
 * PHP scripts into the shm (RAM) and uses the compiled version straight from the RAM. This will increase
 * the rate of page generation time by up to 5 times as it also optimizes many other aspects of php scripts
 * and reduce serverload.
 * The XCache project is lead by mOo who is also a developer of the Lighttpd, the Webserver also known as lighty.
 *
 * @link http://xcache.lighttpd.net/
 * @link http://xcache.lighttpd.net/wiki/XcacheApi
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Xcache extends AbstractCache implements CacheInterface
{
    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        return xcache_isset($key);
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        return xcache_get($key);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key            Identifier for the data
     * @param  mixed   $data           Data to be cached
     * @param  integer $cache_lifetime How long to cache the data, in minutes
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function store($key, $data, $cache_lifetime = 0)
    {
        return xcache_set($key, $data, $cache_lifetime * 60);
    }

    /**
     * Delete data by key from cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean True if the data was successfully removed, false on failure
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }

    /**
     * Get stats and usage Informations for display
     *
     * Seems the XCache API does not provide infos. Are there any cache infos available?
     * @link http://xcache.lighttpd.net/wiki/XcacheApi
     * @todo implement statistics for xcache usage
     */
    public function stats()
    {
    }
}
