<!-- Modal -->
<script src="<?php echo base_url('assets/js/checklist-modal.js'); ?>"></script>
<div class="modal fade" id="popupModal" role="dialog">
    <div class="modal-dialog" style="width:40%">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="hideModal()">
                    <i class="glyphicon glyphicon-remove"></i>
                </button>
                <h4 class="modal-title corpcolor-font">Complete</h4>
            </div>
            <div class="modal-body" style="min-height:400px;">
                <div class="row">
					<div class="col-sm-12 col-xs-12">
			            <p class="form-group">
			            	<label for="modal_date">Complete date <span class="highlight">*</span></label>
			            	<span class="input-group date datetimepicker">
								<input id="modal_date" name="modal_date" type="text" class="form-control input-sm date-mask" placeholder="Complete date" value="<?=date('Y-m-d')?>" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</span>    
			            </p>
						<p class="form-group">
							<label for="modal_remark">Remark</label>
							<textarea id="modal_remark" name="modal_remark" class="form-control input-sm" placeholder="Remark" rows="5"></textarea>
						</p>
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="clickSave()"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->