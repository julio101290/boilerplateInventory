<?= $this->include('load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<!-- Extend from layout index -->
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<!-- Section content -->
<?= $this->section('content') ?>

<?= $this->include('modulesInventory/dataHeadInventory') ?>
<?= $this->include('modulesInventory/productosModalInventory') ?>
<?= $this->include('modulesInventory/modalPayment') ?>
<?= $this->include('modulesInventory/moreInfoRow') ?>
<?= $this->include('modulesProducts/modalCaptureProducts') ?>
<?= $this->include('modulesCustumers/modalCaptureCustumers') ?>

<?= $this->endSection() ?>
