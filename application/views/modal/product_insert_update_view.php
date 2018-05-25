<div class="content-area">
    <div class="container-fluid">
        <div class="row">

            <div class="col-sm-12">
                <form method="post" id="insert-update">
                    <input type="hidden" name="product_id" value="<?=$product->product_id?>" />
                    <input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
                    <div class="fieldset">
                        <div class="row">

                            <div class="col-sm-4 col-xs-12 pull-right">
                                <blockquote>
                                    <h4 class="corpcolor-font">Instructions</h4>
                                    <p><span class="highlight">*</span> is a required field</p>
                                </blockquote>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <h4 class="corpcolor-font">Basic information</h4>
                                <p class="form-group">
                                    <label for="product_vendor_id">Vendor <span class="highlight">*</span></label>
                                    <select id="product_vendor_id" name="product_vendor_id" data-placeholder="Vendor" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($vendors as $key1 => $value1){
                                            $selected = ($value1->vendor_id == $product->product_vendor_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->vendor_id.'"'.$selected.'>'.$value1->vendor_company_name.' '.$value1->vendor_firstname.' '.$value1->vendor_lastname.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_code">Code <span class="highlight">*</span></label>
                                    <input id="product_code" name="product_code" type="text" class="form-control input-sm required" placeholder="Code" value="<?=$product->product_code?>" />
                                </p>
                                <p class="form-group">
                                    <label for="product_wpp_code">WPP code <span class="highlight">*</span></label>
                                    <input id="product_wpp_code" name="product_wpp_code" type="text" class="form-control input-sm required" placeholder="WPP code" value="<?=$product->product_wpp_code?>" />
                                </p>
                                <div class="form-group">
                                    <label for="product_name">Name <span class="highlight">*</span></label>
                                    <input id="product_name" name="product_name" type="text" class="form-control input-sm required" placeholder="Name" value="<?=$product->product_name?>" />
                                </div>
                                <div class="form-group">
                                    <label for="product_detail">Detail <span class="highlight">*</span></label>
                                    <textarea id="product_detail" name="product_detail" class="form-control input-sm required" placeholder="Detail" rows="5"><?=$product->product_detail?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="product_unit_id">Unit</label>
                                    <select id="product_unit_id" name="product_unit_id" data-placeholder="Unit" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($units as $key => $value){
                                            $selected = ($value->unit_id == $product->product_unit_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value->unit_id.'"'.$selected.'>'.$value->unit_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="product_weight">Weight</label>
                                    <input id="product_weight" name="product_weight" type="text" class="form-control input-sm required" placeholder="Weight" value="<?=$product->product_weight?>" />
                                    <small>Eg: 1kg, 1g</small>
                                </div>
                                <div class="form-group">
                                    <label for="product_cost">Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">currency</span>
                                        <input id="product_cost" name="product_cost" type="number" min="0" class="form-control input-sm" placeholder="Cost" value="<?=$product->product_cost?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="product_price">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">HKD</span>
                                        <input id="product_price_hkd" name="product_price_hkd" type="number" min="0" class="form-control input-sm" placeholder="Price HKD" value="<?=$product->product_price_hkd?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="product_link">Link</label>
                                    <input id="product_link" name="product_link" type="text" class="form-control input-sm" placeholder="Link" value="<?=$product->product_link?>" />
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-12">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <?php
                                if( !empty($product->product_id) ) { ?>
                                    <a href="javascript:;" type="button" onclick="clickSave('update')" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Save</a>
                                <?php }else{ ?>
                                    <a href="javascript:;" type="button" onclick="clickSave('insert')" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Save</a>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.chosen-select').chosen();
        $('.chosen-container').css('width', '100%');
    });
</script>