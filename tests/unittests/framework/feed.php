<?php
#namespace Koch\Test;

use Koch\Feed;

class FeedTest extends Clansuite_UnitTestCase
{
    // path to valid rss feed
    public $feed_url = '';

    public function setUp()
    {
        parent::setUp();

        // Test Subject
        require_once TESTSUBJECT_DIR . 'framework/Koch/Feed/Feed.php';

        // valid rss feed online source
        #$this->feed_url = 'http://groups.google.com/group/clansuite/feed/rss_v2_0_msgs.xml';
        $this->feed_url = __DIR__ . '/fixtures/feed/clansuite_rss_v2_0_msgs.xml';
    }

    public function tearDown()
    {
        $cachefile = ROOT_CACHE . md5($this->feed_url);

        if (is_file($cachefile)) {
            unlink($cachefile);
        }
    }

    /**
     * testMethod_fetchRSS()
     */
    public function testMethod_fetchRSS()
    {
        $simplepie_feed_object = Feed::fetchRSS($this->feed_url);

        $this->assertIsA($simplepie_feed_object, 'SimplePie');
    }

    /**
     * testMethod_fetchRawRSS_withoutCaching()
     */
    public function testMethod_fetchRawRSS_withoutCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feed_url, false);

        $this->assertContainsString('title>clansuite.com Google Group</title>', $feedcontent);
    }

    /**
     * testMethod_fetchRawRSS_withCaching()
     */
    public function testMethod_fetchRawRSS_withCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feed_url, true);

        // check for cache file
        $this->assertTrue(is_file(ROOT_CACHE . md5($this->feed_url)));

        // check for content
        $this->assertContainsString('title>clansuite.com Google Group</title>', $feedcontent);
    }

    /**
     * testMethod_getFeedcreator()
     */
    public function testMethod_getFeedcreator()
    {
        $feedcreator_object = Feed::getFeedcreator();

        #$this->assertIsA($feedcreator_object, 'FeedCreator');
        $this->assertIsA($feedcreator_object, 'UniversalFeedCreator');
    }
}
