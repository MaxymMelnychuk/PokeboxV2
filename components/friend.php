<?php
function displayFriend($friend, $isInvited = false, $hasInvitedMe = false, $isFriend = false) {
    ?>
    <div class="wraping">
        <div class="stat"><?php echo $friend['username']; ?></div>
        
        <?php if ($hasInvitedMe): ?>
            <button class="invite" data-userid="<?php echo $friend['iduser']; ?>">Accepter</button>
            <button class="cancel" data-userid="<?php echo $friend['iduser']; ?>">Refuser</button>
        <?php elseif ($isInvited): ?>
            <button class="invite" disabled>Invitation envoyée</button>
            <button class="cancel" data-userid="<?php echo $friend['iduser']; ?>">Annuler</button>
        <?php elseif ($isFriend): ?>
            <button class="invite" disabled>Ami(e)</button>
            <button class="trading" data-userid="<?php echo $friend['username']; ?>">Trade</button>
        <?php else: ?>
            <button class="invite" data-userid="<?php echo $friend['iduser']; ?>">Invite</button>
        <?php endif; ?>
    </div>
    <?php
}
?> 