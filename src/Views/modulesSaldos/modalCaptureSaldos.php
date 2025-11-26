<!-- Modal Saldos -->
  <div class="modal fade" id="modalAddSaldos" tabindex="-1" role="dialog" aria-labelledby="modalAddSaldos" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title"><?= lang('saldos.createEdit') ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form id="form-saldos" class="form-horizontal">
                      <input type="hidden" id="idSaldos" name="idSaldos" value="0">

                                  <div class="form-group row">
                <label for="emitidoRecibido" class="col-sm-2 col-form-label">Empresa</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        </div>

                        <select class="form-control idEmpresa" name="idEmpresa" id="idEmpresa" style = "width:80%;">
                            <option value="0">Seleccione empresa</option>
                            <?php
                            foreach ($empresas as $key => $value) {

                                echo "<option value='$value[id]' selected>$value[id] - $value[nombre] </option>  ";
                            }
                            ?>

                        </select>

                    </div>
                </div>
            </div>
<div class="form-group row">
    <label for="idAlmacen" class="col-sm-2 col-form-label"><?= lang('saldos.fields.idAlmacen') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="idAlmacen" id="idAlmacen" class="form-control <?= session('error.idAlmacen') ? 'is-invalid' : '' ?>" value="<?= old('idAlmacen') ?>" placeholder="<?= lang('saldos.fields.idAlmacen') ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="lote" class="col-sm-2 col-form-label"><?= lang('saldos.fields.lote') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="lote" id="lote" class="form-control <?= session('error.lote') ? 'is-invalid' : '' ?>" value="<?= old('lote') ?>" placeholder="<?= lang('saldos.fields.lote') ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="idProducto" class="col-sm-2 col-form-label"><?= lang('saldos.fields.idProducto') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="idProducto" id="idProducto" class="form-control <?= session('error.idProducto') ? 'is-invalid' : '' ?>" value="<?= old('idProducto') ?>" placeholder="<?= lang('saldos.fields.idProducto') ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="codigoProducto" class="col-sm-2 col-form-label"><?= lang('saldos.fields.codigoProducto') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="codigoProducto" id="codigoProducto" class="form-control <?= session('error.codigoProducto') ? 'is-invalid' : '' ?>" value="<?= old('codigoProducto') ?>" placeholder="<?= lang('saldos.fields.codigoProducto') ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="descripcion" class="col-sm-2 col-form-label"><?= lang('saldos.fields.descripcion') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="descripcion" id="descripcion" class="form-control <?= session('error.descripcion') ? 'is-invalid' : '' ?>" value="<?= old('descripcion') ?>" placeholder="<?= lang('saldos.fields.descripcion') ?>" autocomplete="off">
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="cantidad" class="col-sm-2 col-form-label"><?= lang('saldos.fields.cantidad') ?></label>
    <div class="col-sm-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            </div>
            <input type="text" name="cantidad" id="cantidad" class="form-control <?= session('error.cantidad') ? 'is-invalid' : '' ?>" value="<?= old('cantidad') ?>" placeholder="<?= lang('saldos.fields.cantidad') ?>" autocomplete="off">
        </div>
    </div>
</div>

        
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                  <button type="button" class="btn btn-primary btn-sm" id="btnSaveSaldos"><?= lang('boilerplate.global.save') ?></button>
              </div>
          </div>
      </div>
  </div>

  <?= $this->section('js') ?>


  <script>

      $(document).on('click', '.btnAddSaldos', function (e) {


          $(".form-control").val("");

          $("#idSaldos").val("0");

          $("#btnSaveSaldos").removeAttr("disabled");

      });

      /* 
       * AL hacer click al editar
       */



      $(document).on('click', '.btnEditSaldos', function (e) {


          var idSaldos = $(this).attr("idSaldos");

          //LIMPIAMOS CONTROLES
          $(".form-control").val("");

          $("#idSaldos").val(idSaldos);
          $("#btnGuardarSaldos").removeAttr("disabled");

      });


    $("#idEmpresa").select2();

  </script>


  <?= $this->endSection() ?>
        