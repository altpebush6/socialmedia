<form method="post" id="form_<?= $items->NavForm ?>">
    <th class="py-3 border-end"><?= $items->NavName ?></th>
    <td class="py-3">
        <select name="<?= $items->NavForm ?>" id="<?= $items->NavForm ?>" class="form-select form-select-sm w-75 mx-auto shadow">
            <?php
            $memberCountry = $db->getColumnData("SELECT MemberCountry FROM memberabout WHERE MemberID = ?", array($memberid));
            $countryName = $db->getColumnData("SELECT CountryName FROM countries WHERE CountryID = ?", array($memberCountry));
            if ($memberCountry) { ?>
                <option value="<?= $memberCountry ?>" disabled selected><?= $countryName ?></option>
            <?php } else { ?>
                <option value="0" disabled selected><?= $items->NavOperation ?></option>
            <?php }
            $countries = $db->getDatas("SELECT * FROM countries");
            foreach ($countries as $country) {
            ?>
                <option value="<?= $country->CountryID; ?>"><?= $country->CountryName; ?></option>
            <?php } ?>
        </select>
    </td>
    <td class="py-3 border-start"><button type="button" class="btn btn-sm btn-outline-theme submitabout shadow" onClick='SendFormAbout("form_<?= $items->NavForm ?>","change_<?= $items->NavForm ?>","<?= $items->NavForm ?>")'><?= $translates["save"] ?><span class="spinner" id="<?= $items->NavForm ?>_spinner"></span></button></td>
</form>