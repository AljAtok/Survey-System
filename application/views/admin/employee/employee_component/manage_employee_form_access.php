<div class="container">
    <form method="POST" class="form needs-validation" novalidate>
        <table class="table table-bordered table-striped">
            <input type="hidden" name="employee_id" value="<?= encode($employee->employee_id) ?>">
            <thead>
                <tr>
                    <th colspan="1">Access</th>
                    <th colspan="2">Form Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($forms)): ?>
                    <?php foreach ($forms as $form): ?>
                        <tr>
                            <td colspan="1" >
                                <input 
                                    type="checkbox" 
                                    name="forms[]"
                                    value="<?= encode($form->form_id) ?>"
                                    class="form-check-input"
                                    <?= $form->status ? 'checked' : '' ?>
                                >
                            </td>
                            <td colspan="2" ><?= htmlspecialchars($form->form_name) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No forms available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>