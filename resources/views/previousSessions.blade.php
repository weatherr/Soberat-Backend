<table id='preview'>
<?php foreach($previousSessions as $key => $value): ?>
    <tr id="specificRow">
    <td><p><?php echo $key; ?></p></td>
    <?php foreach($value as $k=>$v): ?>
        <td><p id='drinkk' name="drinkk"><?php echo $k; ?></p></td>
        <td style="text-align:right;"><?php echo $v; ?></td>
    <?php endforeach; ?>
    <!-- <td style="text-align:center;"><?php //echo $value['created_at']; ?></td> -->
    </tr>
<?php endforeach; ?>
</table>
