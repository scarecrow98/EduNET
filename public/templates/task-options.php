<!-- quiz típusú feladatok opciói -->
<section class="section-add-options quiz-options" style="display: none">
    <li class="input-container">
        <label class="label-small">Opció 1:</label>
        <input type="text" name="option-text-1" placeholder="Az válaszlehetőség szövege" required>
        <input type="checkbox" name="option-ans-1" value="1">
    </li>
</section>

<!-- párosítás típusú feladatok opciói -->
<section class="section-add-options pairing-options" style="display: none">
    <li class="input-container">
        <label class="label-small">Opció 1:</label>
        <input type="text" name="option-text-1" placeholder="Az válaszlehetőség szövege" required>
        <input type="text" name="option-ans-1" maxlength="1" placeholder="A helyes válasz betűjele" required>
    </li>
</section>

<!-- igaz/hamis típusú feladatok opciói -->
<section class="section-add-options truefalse-options" style="display: none">
    <li class="input-container">
        <label class="label-small">Opció 1:</label>
        <input type="text" name="option-text-1" placeholder="Az válaszlehetőség szövege" required>
        <input type="radio" name="option-ans-1" value="1">
        <input type="radio" name="option-ans-1" value="0">
    </li>
</section>