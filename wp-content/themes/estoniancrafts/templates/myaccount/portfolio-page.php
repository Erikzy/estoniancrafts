<h1>
	<?= $portfolio->post_title ?>
</h1>
<ul class="dokan-seller-wrap">
<?php foreach ($pictures as $picture) : ?>
	<li class="dokan-single-seller">
        <div class="dokan-store-thumbnail">

            <div class="dokan-store-banner-wrap">
                <a href="">
                    <img class="dokan-store-img" src="<?= $picture['url'] ?>" alt="">
                </a>
            </div>
            <div class="dokan-store-caption">
                <?= $picture['description'] ?>
            </div> <!-- .caption -->
        </div> <!-- .thumbnail -->
    </li> <!-- .single-seller -->
<?php endforeach; ?>
</ul>