<div class="fieldset">
    <div class="search-area">

        <form product="form" method="get">
            <input type="hidden" name="product_id" />
            <table>
                <tbody>
                <tr>
                    <td width="90%">
                        <div class="row search-control">
                            <!-- <div class="col-sm-1"><h6>Product</h6></div> -->
                            <div class="col-sm-2">
                                <input type="text" name="product_id" class="form-control input-sm" placeholder="#" value="" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="product_code_like" class="form-control input-sm" placeholder="Product code" value="" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="product_wpp_code_like" class="form-control input-sm" placeholder="Product wpp code" value="" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="product_name_like" class="form-control input-sm" placeholder="Product name" value="" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="vendor_company_code_like" class="form-control input-sm" placeholder="Vendor company code" value="" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="vendor_company_name_like" class="form-control input-sm" placeholder="Vendor company name" value="" />
                            </div>
                        </div>
                    </td>
                    <td valign="top" width="10%" class="text-right">
                        <a href="javascript:;" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Search" onclick="clickSearch()">
                            <i class="glyphicon glyphicon-search"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>

    </div> <!-- list-container -->
</div>
<div class="fieldset">

    <div class="list-area">
        <form name="list" action="<?=base_url('product/delete')?>" method="post">
            <input type="hidden" name="product_id" />
            <input type="hidden" name="product_delete_reason" />
            <div class="page-area"><?=$pagination?></div>
            <table id="product" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>
                        Company ID
                    </th>
                    <th>
                        Company name
                    </th>
                    <th>
                        <a href="javascript:;" onclick="changeSort('product_code')">
                            Code <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" onclick="changeSort('product_wpp_code')">
                            WPP code <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" onclick="changeSort('product_name')">
                            Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                        </a>
                    </th>
                    <th>
                    	<a href="javascript:;" onclick="changeSort('product_price_hkd')">
                            Price <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                        </a>
                    </th>
                    <th>
                        <a href="javascript:;" onclick="changeSort('product_modify')">
                            Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($products as $key => $value){ ?>
                    <tr>
                        <td title="<?=$value->product_id?>">
                            <input type="checkbox" onclick="clickRecord(<?=$value->product_id?>)" />&nbsp;&nbsp;&nbsp;&nbsp;<?=$key+1?>
                        </td>
                        <td><?=get_vendor($value->product_vendor_id)->vendor_company_code?></td>
                        <td><?=get_vendor($value->product_vendor_id)->vendor_company_name?></td>
                        <td><?=$value->product_code?></td>
                        <td><?=$value->product_wpp_code?></td>
                        <td><?=$value->product_name?></td>
                        <td><?='HKD '.$value->product_price_hkd?></td>
                        <td><?=convert_datetime_to_date($value->product_modify)?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="page-area"><?=$pagination?></div>
        </form>
    </div>
</div>