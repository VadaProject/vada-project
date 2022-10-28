<?php


// TODO: refactor this back into the other claim rendering logic
// The function name is wrong; it doesn't create a modal.

/**
 * This function echoes the HTML element for the Details button
 * used to show the modal.
 */
function createModal($claimID)
{
?>
    <div class="container">
        <button class="btn btn-primary" onclick="loadData(this.getAttribute('data-id'));"
        data-id="<?php echo $claimID; ?>">
        Details
        </button>
    </div>
    <?php
}
