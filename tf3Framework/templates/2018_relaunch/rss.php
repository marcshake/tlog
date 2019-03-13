<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php echo $this->view['title']; ?></title>
        <description>TFF3-Feed </description>
        <link><?php echo $this->view['PAGEROOT'] ?>blog/</link>
        <docs>http://blogs.law.harvard.edu/tech/rss</docs>
        <lastBuildDate><?php echo $this->view['pubDate'] ?></lastBuildDate>
        <pubDate><?php echo $this->view['pubDate'] ?></pubDate>
        <generator>TFF 3 Coding Blogging Framewort</generator>
        <atom:link href="<?= WEB ?>blog/rss" rel="self" type="application/rss+xml"/>

        <?php foreach ($this->view['blogposts'] as $row => $items) : ?>
        
            <item>
                <title><?php echo $items['title'] ?></title>
                <description><?php echo htmlspecialchars($items['description']) ?></description>
                <link><?=WEB?>blog/?p=<?=$items['p_id']?></link>
                <guid><?=WEB?>blog/?p=<?=$items['p_id']?></guid>
                <pubDate><?php echo $items['updated'] ?></pubDate>
                <?php foreach ($items['categories'] as $ggg): ?>
                <category><![CDATA[<?=$ggg['title']?>]]></category>
                <?php endforeach;?>
                <author>info@trancefish.de</author>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
