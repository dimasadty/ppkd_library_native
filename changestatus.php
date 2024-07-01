<div class="modal fade" id="changestatus-<?php echo $dataParticipant['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Change Status- <?php echo $dataParticipant['status'] ?></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="#" method="post">
                <input type="hidden" name="id" value="<?php echo $dataParticipant['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="">Change Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Pilih Status</option>
                            <option value="1">Peserta Lolos</option>
                            <option value="2">Lolos Interview</option>
                            <option value="3">Lolos Administrasi</option>
                            <option value="0">Tidak Lolos</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" name="ubah_status">Change Status</button>
                </div>
            </form>

        </div>
    </div>
</div>