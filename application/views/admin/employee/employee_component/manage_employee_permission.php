<!-- Mock up -->
<div class="container position-relative">
    <!-- Overlay -->
    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.7); z-index: 10;">
        <div class="text-center text-white">
            <h1>🚧 Under Construction 🚧</h1>
            <p>We're working on this feature. Please check back later!</p>
        </div>
    </div>

    <form">
        <div class="form-group">
            <label for="employee_id">Employee ID:</label>
            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
        </div>
        <div class="form-group">
            <label for="permissions">Permissions:</label>
            <select multiple class="form-control" id="permissions" name="permissions[]">
                <option value="view">View</option>
                <option value="edit">Edit</option>
                <option value="delete">Delete</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <hr>
    <h3>Current Permissions</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Permissions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($permissions)): ?>
                <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($permission->employee_id); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $permission->permissions)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">No permissions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>