<?php require __DIR__ . '/../partials/page_start.php'; ?>
<div class="card">
    <table class="table">
        <tr><th>Doctor</th><th>Date</th><th>Diagnosis</th><th>Download</th></tr>
        <?php foreach ($prescriptions as $prescription): ?>
            <tr>
                <td><?= e($prescription['doctor_name']) ?></td>
                <td><?= e(formatDate($prescription['appt_date'])) ?></td>
                <td><?= e(strlen($prescription['diagnosis']) > 80 ? substr($prescription['diagnosis'], 0, 77) . '...' : $prescription['diagnosis']) ?></td>
                <td>
                    <?php if ($prescription['file_path']): ?>
                        <a class="btn" href="<?= e(url('prescriptions', 'download', ['id' => $prescription['appointment_id']])) ?>">Download PDF</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php require __DIR__ . '/../partials/page_end.php'; ?>
