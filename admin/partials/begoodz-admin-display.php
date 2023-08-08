<?php
$rent_limit_time = get_option('rent_limit_time');
?>

<div class="wrap">
    <h1>Réglages</h1>
    <div class="wrap happy_larry_settings">
        <!--Select for the Location Type Category-->
        <div class="setting_division">
            <label for="time_period">Heure limite de location surlendemain :</label>
            <select name="time_period" id="time_period" class="time_period">
                <option value="" disabled >Sélectionnez une option</option>
                <?php for ($i = 0; $i < 24; $i++) : ?>
                    <option value="<?php echo $i ?>" <?php echo $rent_limit_time == $i ? 'selected' : '' ; ?>><?php echo $i < 10 ? "0": ""; echo $i; ?>:00</option>
                <?php endfor; ?>
            </select>
        </div>

        <button id="save_settings" class="save_buttons">Sauver les paramêtres</button>
    </div>
</div>
