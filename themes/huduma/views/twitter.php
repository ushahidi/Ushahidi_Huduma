<?php
/**
 * View page for the Twitter widget
 *
 * PHP version 5
 */
?>
    <script src="http://widgets.twimg.com/j/2/widget.js"></script>
    <script>
    new TWTR.Widget({
      version: 2,
      type: 'search',
      search: '#huduma',
      interval: 6000,
      title: '',
      subject: '#huduma',
      width: 295,
      height: 300,
      theme: {
        shell: {
          background: '#ebebeb',
          color: '#34abd3'
        },
        tweets: {
          background: '#ffffff',
          color: '#444444',
          links: '#1986b5'
        }
      },
      features: {
        scrollbar: false,
        loop: true,
        live: true,
        hashtags: true,
        timestamp: true,
        avatars: true,
        toptweets: true,
        behavior: 'default'
      }
    }).render().start();
    </script>