<!-- File: /app/View/Blogs/news.ctp -->

<?php // This file contains PHP ?>

<h1>Aktuelles</h1>

<?php foreach ($news as $new): ?>
<article class="entry clearfix">


  <div class="entry-body">
    <h1 class="title"><?php echo $this->Html->link($new['title'], $new['url']); ?></h1>
    <div class="index-img">
      <?php echo $this->Html->image($new['image_url'], array('alt' => $new['title'], 'url' => $new['url'],)); ?>
    </div>
  </div><!-- end .entry-body -->

  <div class="entry-meta">
    <ul>
      <li>
        <a href="<?php echo Router::url($new['url'], true); ?>" title="<?php echo h($new['title']); ?>" rel="bookmark">
          <span class="post-format <?php echo strtolower($new['type']) ?>">Permalink</span>
        </a>
      </li>
      <li>
        <span class="title"><?php echo $this->Html->link($new['title'], $new['url']); ?></span>
      </li>
      <li>
        <span class="title">Erstellt:</span>
        <?php echo $this->Html->getDateTime($new['created'], array('year' => 'true', 'day' => true)); ?>
      </li>
    </ul>
  </div><!-- end .entry-meta -->
</article><!-- end .entry -->
<?php endforeach; ?>
<?php unset($new); ?>

