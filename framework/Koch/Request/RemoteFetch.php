<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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

namespace Koch\Request;

/**
 * Koch Framework Remote Request Manager
 *
 * 1: Snoppy
 * 2: cURL
 * 3: Remote
 * (4: FTP)
 */
class RemoteFetch
{
    /**
     * Fetches remote content with Snoopy
     *
     * @param $url URL of remote content to fetch
     */
    public static function snoopy_get_file($url)
    {
        $remote_content = null;

        if (Koch_Loader::loadLibrary('snoopy')) {
            $s = new Snoopy();
            $s->fetch($url);

            if ($s->status == 200) {
                $content = $s->results;
            }
        }

        if (false === empty($content)) {
            return $content;
        } else {
            return false;
        }
    }

    /**
     * Fetches remote content with cURL
     *
     * @param $url URL of remote content to fetch
     */
    public static function curl_get_file($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $content = curl_exec($curl);
        curl_close($curl);

        if (false === empty($content)) {
            return $content;
        } else {
            return false;
        }
    }

    /**
     * Fetches remote content with file_get_contents
     *
     * @param $url URL of remote content to fetch
     * @param $flags
     * @param $context
     */
    public static function remote_get_file($url, $flags = null, $context = null)
    {
        #if(true === ini_get('allow_url_fopen'))
        #{
            $context = stream_context_create(array('http' => array('timeout' => 15)));
            $content = file_get_contents($url, $flags, $context);
        #}

        if (false === empty($content)) {
            return $content;
        } else {
            return false;
        }
    }

    /**
     * Updates a local file with the content of a remote file,
     * when the sha1 checksums do not match.
     */
    public static function updateFileIfDifferent($remote_file, $local_file)
    {
        $data = self::remote_get_file($remote_file);

        if ($data !== false) {
            if (sha1($data) !== sha1_file($local_file)) {
                file_put_contents($local_file, $data);
            }
        }
    }
}
