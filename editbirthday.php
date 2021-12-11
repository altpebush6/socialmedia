<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3 px-5">
        <div class="col-10 offset-1">
            <input class="form-control form-control-sm" type="date" name="<?= $items->NavForm ?>" id="<?= $items->NavForm ?>_input" value="<?= $db->getColumnData("SELECT $getItem FROM memberabout WHERE MemberID = ?", array($memberid)) ?>">
        </div>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitabout" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>