<style>
    .vertical-alignment-helper {
        display:table;
        height: 100%;
        width: 100%;
        pointer-events:none;
    }
    .vertical-align-center {
        /* To center vertically */
        display: table-cell;
        vertical-align: middle;
        pointer-events:none;
    }
    .modal-content {
        /* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
        width:inherit;
        height:inherit;
        /* To center horizontally */
        margin: 0 auto;
        pointer-events:all;
    }
    .modal-backdrop {
        display: none;
    }
</style>
<h1>
	<?= $portfolio->post_title ?>
</h1>
<ul class="dokan-seller-wrap">
    <?php 
    foreach ($pictures as $picture) :
        $uniqId = uniqid();

     ?>
       <li class="dokan-single-seller">
        <div class="dokan-store-thumbnail">

            <div class="dokan-store-banner-wrap">
                <a href="#" data-toggle="modal" data-target="#imgModal_<?= $uniqId ?>">
                    <img class="dokan-store-img" src="<?= $picture['url'] ?>" alt="">
                </a>
            </div>
            <div class="dokan-store-caption">
                <?= $picture['description'] ?>
            </div> <!-- .caption -->
        </div> <!-- .thumbnail -->

        <div id="imgModal_<?= $uniqId ?>" class="modal fade" role="dialog">
            <!-- Modal content-->
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center">
                    <div class="modal-content text-center">
                        <img src="<?= $picture['url']?>" alt="img">
                    </div>
                </div>
            </div>
        </div>

    </li> <!-- .single-seller -->
<?php endforeach; ?>
</ul>