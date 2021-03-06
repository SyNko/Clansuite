<?php

/**
 * Clansuite - just an eSports CMS
 * Jens-Andr� Koch � 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
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

class Clansuite_CodeCoverage
{
    public static $coverage;

    /**
    * Starts the Code Coverage.
    */
    public static function start()
    {
        /**
        * Simpletest Code Coverage depends on xdebug.
        *
        * Ensure that the xdebug extension is loaded.
        */
        if (false === extension_loaded('xdebug')) {
            die('Code Coverage needs Xdebug extension. Not loaded!');
        }

        if (false === function_exists("xdebug_start_code_coverage")) {
            die('Code Coverage needs the method xdebug_start_code_coverage. Not found!');
        }

        /**
        * Simpletest Code Coverage depends on sqlite.
        *
        * Ensure that the sqlite extension is loaded.
        */
        if (false === class_exists('SQLiteDatabase')) {
            echo 'Code Coverage needs the php extension SQLITE. Not loaded!';
        }

        /**
        * Setup Simpletest Code Coverage.
        */
        require_once 'simpletest/extensions/coverage/coverage.php';

        $coverage = new CodeCoverage();
        $coverage->log = 'coverage.sqlite';
        $coverage->root = TESTSUBJECT_DIR;
        $coverage->includes[] = '.*\.php$';
        $coverage->excludes[] = 'simpletest';
        $coverage->excludes[] = 'tests';
        $coverage->excludes[] = 'libraries';
        $coverage->excludes[] = 'coverage-report';
        $coverage->excludes[] = 'sweety';
        $coverage->excludes[] = './.*.php';
        $coverage->maxDirectoryDepth = 1;
        $coverage->resetLog();
        $coverage->writeSettings();

        /**
        * Finally: let's start the Code Coverage.
        */
        $coverage->startCoverage();
        #echo 'Code Coverage started...';

        self::$coverage = $coverage;
    }

    /**
    * Stops the Code Coverage.
    */
    public static function stop()
    {
        self::$coverage->writeUntouched();
        self::$coverage->stopCoverage();
        #echo 'Code Coverage stopped!';
    }

    /**
    * Generates the Code Coverage Report.
    */
    public static function getReport()
    {
        require_once 'simpletest/extensions/coverage/coverage_reporter.php';

        $handler = new CoverageDataHandler(self::$coverage->log);
        $report = new CoverageReporter();
        $report->reportDir = 'coverage-report';
        $report->title = 'Clansuite Coverage Report';
        $report->coverage = $handler->read();
        $report->untouched = $handler->readUntouchedFiles();
        $report->generate();
    }
}
