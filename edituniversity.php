<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <select name="<?= $items->NavForm ?>" id="<?= $items->NavForm ?>" class="form-select form-select-sm w-75 mx-auto">
            <option value="0" disabled selected><?= $items->NavOperation ?></option>
            <?php
            $universities = $db->getDatas("SELECT * FROM universities");
            foreach ($universities as $university) {
            ?>
                <option value="<?= $university->UniversityID; ?>"><?= $university->UniversityName; ?></option>
            <?php } ?>
        </select>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-primary submitabout" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>