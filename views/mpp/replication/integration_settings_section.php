<table class="form-table">
    <tbody>
    <tr valign="top">
        <th width="35%" align="left">
            <label for="replication_cf7_forms">Intercepted Contact Form 7 Forms</label>
        </th>
        <td align="left">
            <select name="replication_cf7_forms[]" multiple>
                <?php foreach ($this->all_cf7_forms as $form): ?>
                    <option value="<?php echo $form->ID; ?>"
                            <?php if (in_array($form->ID, $this->replication_cf7_forms)): ?>selected<?php endif; ?>>
                        <?php echo $form->post_title; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">Specify Contact Form 7 forms to intercept submission to replicated distributor
                info.</p>
        </td>
    </tr>
    </tbody>
</table>
<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/28/2016
 * Time: 12:18 AM
 */
