<?php
    /*Actions on slideshow
        1.add new
        2.update
        3.delete
    */
    $error = -1; /*-1 : Normal, 1 : Error, 0 : Succcess*/
    if(isset($_GET['action']))
    {
        $action=$_GET['action'];
        switch($action)
        {
            case "1":
                $cateid = $_POST['categories'];
                $name =$_POST['proName'];
                $price =$_POST['proPrice'];
                $qty =$_POST['proStock'];
                $brand =$_POST['proBrand'];
                $desc =$_POST['proDesc'];
                
                $img ="noimage.jpg";
                if(file_exists(isset($_FILES['proimg']['tmp_name'])) || is_uploaded_file($_FILES['proimg']['tmp_name']))
                {
                    if(($_FILES['proimg']['size'])/(1048576) <=3 )
                    {
                        $ext = pathinfo($_FILES['proimg']['name'], PATHINFO_EXTENSION);
                        if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                            $img = floor(microtime(true)*1000) . "." . $ext;
                            $tmp_name =$_FILES["proimg"]["tmp_name"];
                            $sourceProperties = getimagesize($tmp_name);
                            $width = $sourceProperties[0];
                            $height = $sourceProperties[1];
                            $tarwidth = 60;
                            $tarheight = 80;
                            $imageType = $sourceProperties[2];
                            $destination  = "../images/products";
                            createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                            move_uploaded_file($tmp_name,$destination."/".$img);
                            $sql = "INSERT INTO tbproducts(proName, proPrice, proimg, proStock, proBrand, proDesc, cateId) VALUES('$name',$price,'$img','$qty','$brand','$desc',$cateid)";
                            if($conn->exec($sql)){
                                $error = 0;
                                $errmsg = "A product has been added successfully!";
                            }else{
                                $error = 1;
                                $errmsg = "Fail to add the product!";
                            }
                        }else{
                            $error = 1;
                            $errmsg = "Only image file is allowd! ";
                        }
                    }else{
                        $error = 1;
                        $errmsg = "File cannot exceed 3MB!";
                    }
                }
                break;
            case "2":
                $proid =$_GET['proid'];
                $name = $_POST['editproName'];
                $desc = $_POST['editproDesc'];
                $price = $_POST['editproPrice'];
                $qty = $_POST['editproStock'];
                $brand = $_POST['editproBrand'];
                $cateid = $_POST['editcategories'];

                
                if($name != "")
                {
                    if(file_exists(isset($_FILES['editproImg']['tmp_name'])) || is_uploaded_file($_FILES['editproImg']['tmp_name']))
                    {
                        if(($_FILES['editproImg']['size'])/(1048576) <=3 )
                        {
                            $ext = pathinfo($_FILES['editproImg']['name'], PATHINFO_EXTENSION);
                            if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                                $img = floor(microtime(true)*1000) . "." . $ext;
                                $tmp_name =$_FILES["editproImg"]["tmp_name"];
                                $sourceProperties = getimagesize($tmp_name);
                                $width = $sourceProperties[0];
                                $height = $sourceProperties[1];
                                $tarwidth = 200;
                                $tarheight = 150;
                                $imageType = $sourceProperties[2];
                                $destination  = "../images/products";
                                createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                                move_uploaded_file($tmp_name,$destination."/".$img);
                                $sql ="UPDATE tbproducts SET proName='$name', proPrice=$price, proImg='$img', proStock=$qty,proBrand='$brand',proDesc='$desc',cateId=$cateid WHERE  proid = $proid ";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                if($stmt->rowCount()>0){
                                    $oldimgname = $_POST['txtoldimg'];
                                    $oldimg = $destination . "/" . $oldimgname;
                                    $oldimgthumbnail = $destination . "/thumbnail/" . $oldimgname;
                                    if(file_exists($oldimg)){
                                        unlink($oldimg);
                                    }
                                    if(file_exists($oldimgthumbnail)){
                                        unlink($oldimgthumbnail);
                                    }

                                    $error = 0;
                                    $errmsg = "A product has been updated successfully!";
                                }else{
                                    $error = 1;
                                    $errmsg = "Fail to update the product!";
                                }
                            }else{
                                $error = 1;
                                $errmsg = "Only image file is allowd! ";
                            }
                        }else{
                            $error = 1;
                            $errmsg = "File cannot exceed 3MB!";
                        }
                    }
                    else
                    {
                        $sql ="UPDATE tbproducts SET proName='$name', proPrice=$price, proStock=$qty,proBrand='$brand',proDesc='$desc',cateId=$cateid WHERE  proid = $proid ";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        if($stmt->rowCount()>0){
                            $error = 0;
                            $errmsg = "A product has been updated successfully!";
                        }else{
                            $error = 1;
                            $errmsg = "Fail to update the product!";
                        }
                    }
                    
                }

                break;
            case "3":
                $img=$_GET['img'];
                $proid=$_GET['proid'];
                $sql ="DELETE FROM tbproducts WHERE proid=$proid";
                if($conn->exec($sql)){
                    $imgname ="../images/products/$img";
                    $imgthumbnail ="../images/products/thumbnail/$img";
                    if(file_exists($imgname)){
                        unlink($imgname);
                    }
                    if(file_exists($imgthumbnail)){
                        unlink($imgthumbnail);
                    }
                    $error = 0;
                    $errmsg="A product has been deleted successfully!";
                    
                }else{
                    $error = 1;
                    $errmsg = "Fail to delete the product!";
                }
                break;
        }
    }

    $sql="SELECT * FROM tbproducts";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();
    $numpage = ceil($num/MAXPERPAGE);
    $pg = 1;
    $offset = 0;
    if(isset($_GET['pg']))
    {
        $pg = $_GET['pg'];
        $offset = ($pg-1) * MAXPERPAGE;
    }

    
        $sql="SELECT tbproducts.*,tbcategory.cateName AS catename
          FROM tbproducts
          LEFT JOIN tbcategory
          ON tbproducts.cateId=tbcategory.cateId
          ORDER BY tbproducts.proid ASC limit ". MAXPERPAGE." offset $offset";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    

?>
    
    <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Code For Slideshow -->
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <h5 class="card-header">Product</h5>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <button
                            type="button"
                            class="btn btn-success fw-bold text-center my-auto me-4"
                            data-bs-toggle="modal"
                            data-bs-target="#create_product"
                            >
                            <i class='menu-icon bx bxs-message-square-add me-2'></i>Create Product
                        </button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <?php include "msgbox.php";?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Descrtiption</th>
                                <th>Brand</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php 
                                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                    $i=$offset+1;
                                    foreach($stmt->fetchAll()as $row){
                                ?>
                                <tr>
                                    <td id="proid-<?=$row['proid']?>"  data-value="<?=$row['proid']?>" ><?= $i++ ?></td>
                                    <td id="img-<?=$row['proid']?>" data-value="<?=$row['proImg']?>"><img src="../images/products/thumbnail/<?= $row["proImg"]?>" alt="Avatar" style="width: 50px; height:70px; "/></td>
                                    <td id="name-<?=$row['proid']?>" data-value="<?=$row['proName'] ?>"><strong><?= $row["proName"]?></strong></td>
                                    <td id="cateid-<?=$row['proid']?>" data-value="<?=$row['cateId'] ?>"><?=($row['cateId'] ? $row['catename']:"No Category")?></td>
                                    <td id="price-<?=$row['proid']?>" data-value="<?=$row['proPrice'] ?>">$<?= $row["proPrice"]?></td>
                                    <td id="qty-<?=$row['proid']?>" data-value="<?=$row['proStock'] ?>"><?= $row["proStock"]?></td>
                                    <td id="desc-<?=$row['proid']?>" data-value="<?=$row['proDesc'] ?>"><?= substr($row["proDesc"],0,15)?>...</td>
                                    <td id="brand-<?=$row['proid']?>" data-value="<?=$row['proBrand'] ?>"><?= $row["proBrand"]?></td>
                                    <td id="rate-<?=$row['proid']?>" data-value="<?=$row['proRating'] ?>"><?= $row["proRating"]?></td>
                                    <td id="review-<?=$row['proid']?>" data-value="<?=$row['proReview'] ?>"><?= $row["proReview"]?></td>
                                    <td style="font-size: 20px;">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#update_product" onclick="loadDataForEdit('<?= $row['proid']?>')" title="Edit"><i class="bx bx-edit-alt me-1"></i></a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="updateDeleteLink('<?= $row['proImg']?>','<?= $row['proid']?>','<?=$pg?>')" title="Delete"><i class="bx bx-trash me-1"></i></a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <!-- / Content -->


    <!-- Create Slideshow Modal -->

        <!-- Modal -->
        <div class="modal fade" id="create_product" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Create Product</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                </div>
                <div class="modal-body">

                    <!-- Form -->
                    <form action="index.php?p=product&action=1" method="post" enctype="multipart/form-data">
                        <!-- 1st row -->
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    class="form-control"
                                    placeholder="Product Name"
                                    name="proName"
                                />
                            </div>
                            <div class="col mb-3">
                                <label for="price" class="form-label">Product Price</label>
                                <input
                                    type="text"
                                    id="price"
                                    class="form-control"
                                    placeholder="Product Price"
                                    name="proPrice"
                                />
                            </div>
                            
                        </div>
                        <!-- 2nd -->
                        <div class="row">
                            <div class="col mb-3">
                                <label for="quantity" class="form-label">Product Quantity</label>
                                <input
                                    type="text"
                                    id="quantity"
                                    class="form-control"
                                    placeholder="Product Quantity"
                                    name="proStock"
                                />
                            </div>

                            <div class="col mb-3">
                                <label for="brand" class="form-label">Product Brand</label>
                                <input
                                    type="text"
                                    id="brand"
                                    class="form-control"
                                    placeholder="Product Brand"
                                    name="proBrand"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <select class="form-select" id="categories" name="categories">
                                        <option selected>Choose Category...</option>
                                        <?php
                                            $sql= "SELECT tbcategory.cateId as Cid, tbcategory.cateName AS catename
                                                   FROM tbproducts
                                                   RIGHT JOIN tbcategory
                                                   ON tbproducts.cateId=tbcategory.cateId 
                                                   GROUP BY tbcategory.cateId";
                                            $stmt =$conn->prepare($sql);
                                            $stmt->execute();
                                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                            foreach($stmt->fetchAll() as $row){
                                        ?>
                                            <option value="<?=$row['Cid']?>"><?=$row['catename']?></option>
                                        <?php
                                             }
                                        ?>
                                        
                                    </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="description" class="form-label">Product Description</label>
                                <textarea name="proDesc" id="description" rows="4" class="form-control" placeholder="Product Descriptiion" style="resize: none"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                            <label for="proimg" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Product Image</label>
                            <input type="file" name="proimg" id="proimg" class="form-control">
                            </div>
                        </div>
                        


                        <button type="button" class="btn btn-secondary float-end "data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary me-3 float-end">Create</button>
                    </form>
                </div>
                </div>
            
            </div>
            </div>
        </div>

    <!-- End Slideshow Modal -->

    <!-- Update Category Modal -->

        <!-- Modal -->
        <div class="modal fade" id="update_product" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Update Category</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                </div>
                <div class="modal-body">

                    <!-- Form -->
                    <form action="index.php?p=product&action=2" method="post" id="proeditform" enctype="multipart/form-data">
                        <div class="row">
                             <!-- name -->
                            <div class="col mb-3">
                                <label for="title" class="form-label">Category Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Category Name"
                                    name="editproName"
                                    id="editproName"
                                />
                            </div>
                            <!-- price -->
                            <div class="col mb-3">
                                <label for="slide_order" class="form-label">price</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Price"
                                    id="editproPrice"
                                    name="editproPrice"
                                />
                            </div>
                             
                        </div>

                        <div class="row">
                            <!-- brand -->
                            <div class="col mb-3">
                                <label for="slide_order" class="form-label">Brand</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Brand"
                                    id="editproBrand"
                                    name="editproBrand"
                                />
                            </div>
                            <!-- Qty -->
                            <div class="col mb-3">
                                <label for="slide_order" class="form-label">product Stock</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Stock"
                                    id="editproStock"
                                    name="editproStock"
                                />
                            </div>
                        </div>
                        <!-- category -->
                        <div class="row">
                            <div class="col-6 mb-3">
                                    <label for="editcategories" class="form-label">Category Name</label>
                                    <select class="form-select" id="editcategories" name="editcategories">
                                        <option value="" selected>Choose Category...</option>
                                        <?php
                                            $sql= "SELECT tbcategory.cateId as Cid, tbcategory.cateName AS catename
                                                   FROM tbproducts
                                                   RIGHT JOIN tbcategory
                                                   ON tbproducts.cateId=tbcategory.cateId 
                                                   GROUP BY tbcategory.cateId";
                                            $stmt =$conn->prepare($sql);
                                            $stmt->execute();
                                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                            foreach($stmt->fetchAll() as $row){
                                        ?>
                                            <option value="<?=$row['Cid']?>"><?=$row['catename']?></option>
                                        <?php
                                             }
                                        ?>
                                        
                                    </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- desc -->
                            <div class="col mb-3">
                                <label for="editproDesc" class="form-label">Product Description</label>
                                <textarea name="editproDesc" id="editproDesc" rows="4" class="form-control" placeholder="Product Descriptiion" style="resize: none"></textarea>
                            </div>
                        </div>
                        <!-- image -->
                        <div class="row">
                            <div class="col mb-3">
                            <label for="editproImg" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Category Image</label>
                            <input type="file" name="editproImg" id="editproImg" class="form-control">
                            </div>
                        </div>

                        <!-- show image -->
                        <div class="row">
                            <div class="col mb-3">
                                <img src="#" id="oldimg" style="width: 60px; height:80px; " />
                                <p id="oldimgname" ></p>
                            </div>
                        </div>
                        <!-- hidden old image name -->
                        <input type="hidden" value="" id="txtoldimg" name="txtoldimg">
                        
                        <button type="button" class="btn btn-secondary float-end "data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary me-3 float-end">Update</button>
                </div>
                </div>
            
            </div>
        </div>
    <!-- End Update Category Modal -->

    <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this slideshow?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="deletelink" type="button" class="btn btn-primary text-light">Yes</a>
                        <a href="#" type="button" class="btn btn-secondary text-light" data-bs-dismiss="modal">No</a>
                    </div>
                    </div>
                </div>
        </div>

    <!--End of Delete Modal -->

    <script>
        function _(obj)
        {
            return document.getElementById(obj); 
        }
        function updateDeleteLink(img,proid,pg){
            _("deletelink").href ="index.php?p=product&action=3&img="+img+"&proid="+proid;
        }
        function loadDataForEdit(proid){
            var name = _("name-"+proid).getAttribute("data-value");
            _("editproName").value = name;
            var desc = _("desc-"+proid).getAttribute("data-value");
            _("editproDesc").value = desc;

            var cate = _("cateid-"+proid).getAttribute("data-value");
            _("editcategories").value = cate;

            var price = _("price-"+proid).getAttribute("data-value");
            _("editproPrice").value = price;
           
            var brand = _("brand-"+proid).getAttribute("data-value");
            _("editproBrand").value = brand;

            var qty = _("qty-"+proid).getAttribute("data-value");
            _("editproStock").value = qty;

            var img = _("img-"+proid).getAttribute("data-value");
            _("oldimg").src="../images/products/thumbnail/"+img;
            _("oldimgname").innerHTML=img;
            _("proeditform").action = _("proeditform").action + "&proid=" + proid ;
            _("txtoldimg").value = img;
        }
    </script>