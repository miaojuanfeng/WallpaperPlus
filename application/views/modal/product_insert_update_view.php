<div class="content-area">
    <div class="container-fluid">
        <div class="row">

            <h2 class="col-sm-12"><a href="<?=base_url('product')?>">Product preset</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> product</h2>

            <div class="col-sm-12">
                <form method="post">
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
                                    <label for="product_unit">Unit</label>
                                    <select id="product_unit" name="product_unit" data-placeholder="Unit" class="chosen-select required">
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
                                <h4 class="corpcolor-font">Related information</h4>
                                <p class="form-group">
                                    <label for="product_brand_id">Brand <span class="highlight">*</span></label>
                                    <select id="product_brand_id" name="product_brand_id" data-placeholder="Brand" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($brands as $key1 => $value1){
                                            $selected = ($value1->brand_id == $product->product_brand_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->brand_id.'"'.$selected.'>'.$value1->brand_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_category_id">Category <span class="highlight">*</span></label>
                                    <select id="product_category_id" name="product_category_id" data-placeholder="Category" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_color_id">Color <span class="highlight">*</span></label>
                                    <select id="product_color_id" name="product_color_id" data-placeholder="Color" class="chosen-select required" multiple="multiple">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_style_id">Style <span class="highlight">*</span></label>
                                    <select id="product_style_id" name="product_style_id" data-placeholder="Style" class="chosen-select required" multiple="multiple">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_usage_id">Usage <span class="highlight">*</span></label>
                                    <select id="product_usage_id" name="product_usage_id" data-placeholder="Usage" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_material_id">Material <span class="highlight">*</span></label>
                                    <select id="product_material_id" name="product_material_id" data-placeholder="Material" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_keyword_id">Keyword <span class="highlight">*</span></label>
                                    <select id="product_keyword_id" name="product_keyword_id" data-placeholder="Keyword" class="chosen-select required" multiple="multiple">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_size_id">Size <span class="highlight">*</span></label>
                                    <select id="product_size_id" name="product_size_id" data-placeholder="Size" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($categorys as $key1 => $value1){
                                            $selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_type_id">Type <span class="highlight">*</span></label>
                                    <select id="product_type_id" name="product_type_id" data-placeholder="Type" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($types as $key1 => $value1){
                                            $selected = ($value1->type_id == $product->product_type_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->type_id.'"'.$selected.'>'.$value1->type_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="form-group">
                                    <label for="product_team_id">Team <span class="highlight">*</span></label>
                                    <select id="product_team_id" name="product_team_id" data-placeholder="Team" class="chosen-select required">
                                        <option value></option>
                                        <?php
                                        foreach($teams as $key1 => $value1){
                                            $selected = ($value1->team_id == $product->product_team_id) ? ' selected="selected"' : "" ;
                                            echo '<option value="'.$value1->team_id.'"'.$selected.'>'.$value1->team_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                                <!-- <p class="form-group">
											<label for="product_vendor_id">Vendor <span class="highlight">*</span></label>
											<select id="product_vendor_id" name="product_vendor_id" data-placeholder="Vendor" class="chosen-select required">
												<option value></option>
												<?php
                                foreach($vendors as $key1 => $value1){
                                    $selected = ($value1->vendor_id == $product->product_vendor_id) ? ' selected="selected"' : "" ;
                                    echo '<option value="'.$value1->vendor_id.'"'.$selected.'>'.$value1->vendor_company_name.'</option>';
                                }
                                ?>
											</select>
										</p> -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>