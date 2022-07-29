<?php
use common\models\UserStudent;
/**
 * @var UserStudent[] $children
 */
?>
parent/index
<ul>
<?php foreach ($children as $child): ?>
    <li><?= $child->name . ' (' . $child->studentLogin->username . ', ' . $child->studentLogin->password . ')' ?></li>
<?php endforeach ?>
</ul>
