<?php
function displayCard($card) {
    ?>
    <div class='pokemon'>
        <img class="image-pokemon" src="<?php echo $card['url']; ?>">
        <div class="description">
            <div class="name"><?php echo $card['card_name']; ?></div>
            <div class="stats">
                <div class="stat">Quantité : <?php echo $card['quantity']; ?></div>
            </div>
        </div>
    </div>
    <?php
}
?> 