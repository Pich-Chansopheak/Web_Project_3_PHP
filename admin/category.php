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
                $name = $_POST['cateName'];
                $desc =$_POST['cateDesc'];

                $img ="noimage.jpg";
                if($name!=""){
                    if(file_exists(isset($_FILES['cateImg']['tmp_name'])) || is_uploaded_file($_FILES['cateImg']['tmp_name']))
                    {
                        if(($_FILES['cateImg']['size'])/(1048576) <=3 )
                        {
                            $ext = pathinfo($_FILES['cateImg']['name'], PATHINFO_EXTENSION);
                            if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                                $img = floor(microtime(true)*1000) . "." . $ext;
                                $tmp_name =$_FILES["cateImg"]["tmp_name"];
                                $sourceProperties = getimagesize($tmp_name);
                                $width = $sourceProperties[0];
                                $height = $sourceProperties[1];
                                $tarwidth = 200;
                                $tarheight = 150;
                                $imageType = $sourceProperties[2];
                                $destination  = "../images/categories";
                                createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                                move_uploaded_file($tmp_name,$destination."/".$img);
                                
                                $sql = "INSERT INTO tbcategory( cateName, cateImg, cateDesc) VALUES('$name','$img','$desc')";
                                if($conn->exec($sql)){
                                    $error = 0;
                                    $errmsg = "A category has been added successfully!";
                                }else{
                                    $error = 1;
                                    $errmsg = "Fail to add the category!";
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
                }
                break;
            case "2":
                $catid =$_GET['cateid'];
                $name =$_POST['editcateName'];
                $desc =$_POST['editcateDesc'];
                if($name != "")
                {
                    if(file_exists(isset($_FILES['editcateImg']['tmp_name'])) || is_uploaded_file($_FILES['editcateImg']['tmp_name']))
                    {
                        if(($_FILES['editcateImg']['size'])/(1048576) <=3 )
                        {
                            $ext = pathinfo($_FILES['editcateImg']['name'], PATHINFO_EXTENSION);
                            if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                                $img = floor(microtime(true)*1000) . "." . $ext;
                                $tmp_name =$_FILES["editcateImg"]["tmp_name"];
                                $sourceProperties = getimagesize($tmp_name);
                                $width = $sourceProperties[0];
                                $height = $sourceProperties[1];
                                $tarwidth = 200;
                                $tarheight = 150;
                                $imageType = $sourceProperties[2];
                                $destination  = "../images/categories";
                                createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                                move_uploaded_file($tmp_name,$destination."/".$img);
                                $sql ="UPDATE tbcategory SET cateName='$name', cateImg='$img', cateDesc='$desc' WHERE  cateId = $catid ";
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
                                    $errmsg = "A category has been updated successfully!";
                                }else{
                                    $error = 1;
                                    $errmsg = "Fail to update the category!";
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
                        $sql ="UPDATE tbcategory SET cateName='$name', cateDesc='$desc' WHERE  cateId = $catid  ";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        if($stmt->rowCount()>0){
                            $error = 0;
                            $errmsg = "A category has been updated successfully!";
                        }else{
                            $error = 1;
                            $errmsg = "Fail to update the category!";
                        }
                    }
                    
                }

                break;
            case "3":
                $img=$_GET['img'];
                $cateid=$_GET['cateid'];
                $sql ="DELETE FROM tbcategory WHERE cateId=$cateid";
                if($conn->exec($sql)){
                    $imgname ="../images/categories/$img";
                    $imgthumbnail ="../images/categories/thumbnail/$img";
                    if(file_exists($imgname)){
                        unlink($imgname);
                    }
                    if(file_exists($imgthumbnail)){
                        unlink($imgthumbnail);
                    }
                    $error = 0;
                    $errmsg="A category has been deleted successfully!";
                    
                }else{
                    $error = 1;
                    $errmsg = "Fail to delete the category!";
                }
                break;
        }
    }

    $sql="SELECT * FROM tbcategory ORDER BY cateId ASC";
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

    $sql="SELECT tbcategory.*,COUNT(tbproducts.cateId) AS num
          FROM tbcategory
          LEFT JOIN tbproducts
          ON tbcategory.cateId=tbproducts.cateId
          GROUP BY tbcategory.cateId 
          ORDER BY tbcategory.cateId ASC limit ". MAXPERPAGE." offset $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

?>
    
    <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
        <!-- code for category -->
        <div class="card">
                <div class="row">
                    <div class="col-6">
                        <h5 class="card-header">Category</h5>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <button
                            type="button"
                            class="btn btn-success fw-bold text-center my-auto me-4"
                            data-bs-toggle="modal"
                            data-bs-target="#create_category"
                            >
                            <i class='menu-icon bx bxs-message-square-add me-2'></i>Create Category
                        </button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                   <?php include "msgbox.php" ;?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Number OF Product</th>
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
                                <td id="catid-<?=$row['cateId']?>"  data-value="<?=$row['cateId']?>" ><?= $i++ ?></td>
                                <td id="img-<?=$row['cateId']?>" data-value="<?=$row['cateImg']?>"><img src="../images/categories/thumbnail/<?= $row["cateImg"]?>" alt="Avatar" style="width: 100px; height:60px; "/></td>
                                <td id="name-<?=$row['cateId']?>" data-value="<?=$row['cateName'] ?>"><strong><?= $row["cateName"]?></strong></td>
                                <td id="desc-<?=$row['cateId']?>" data-value="<?=$row['cateDesc'] ?>"><?= substr($row["cateDesc"],0,15)?>...</td>
                                <td id="num-<?=$row['cateId']?>" data-value="<?=$row['num'] ?>"><?= $row["num"]?></td>
                                <td style="font-size: 20px;">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#update_category" onclick="loadDataForEdit('<?= $row['cateId']?>')" title="Edit"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="updateDeleteLink('<?= $row['cateImg']?>','<?= $row['cateId']?>','<?=$pg?>')" title="Delete"><i class="bx bx-trash me-1"></i></a>
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

    <!-- Create  Category Modal -->

                <!-- Modal -->
                <div class="modal fade" id="create_category" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Create Category</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                        </div>
                        <div class="modal-body">

                            <!-- Form -->
                            <form action="index.php?p=category&action=1" method="post" enctype="multipart/form-data">
                                <!-- Name -->
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="title" class="form-label">Category Name</label>
                                        <input
                                            type="text"
                                            id="title"
                                            class="form-control"
                                            placeholder="Category Name"
                                            name="cateName"
                                        />
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="slide_order" class="form-label">Description</label>
                                        <input
                                            type="text"
                                            id="slide_order"
                                            class="form-control"
                                            placeholder="Description"
                                            name="cateDesc"
                                        />
                                    </div>
                                </div>
                                <!-- image -->
                                <div class="row">
                                    <div class="col mb-3">
                                    <label for="cate_img" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Category Image</label>
                                    <input type="file" name="cateImg" id="cate_img" class="form-control">
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
    <!-- End  Category Modal -->

    <!-- Update Category Modal -->

                <!-- Modal -->
                <div class="modal fade" id="update_category" tabindex="-1" aria-hidden="true">
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
                            <form action="index.php?p=category&action=2" method="post" id="cateditform" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="title" class="form-label">Category Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Category Name"
                                            name="editcateName"
                                            id="editcateName"
                                        />
                                    </div>
                                    
                                </div>

                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="slide_order" class="form-label">Description</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Description"
                                            id="editcateDesc"
                                            name="editcateDesc"
                                        />
                                    </div>
                                </div>
                            

                                <div class="row">
                                    <div class="col mb-3">
                                    <label for="editcateimg" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Category Image</label>
                                    <input type="file" name="editcateImg" id="editcateimg" class="form-control">
                                    </div>
                                </div>

                                <!-- show image -->
                                <div class="row">
                                    <div class="col mb-3">
                                        <img src="#" id="oldimg" style="width: 100px; height:60px; " />
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
        function updateDeleteLink(img,cateid,pg){
            _("deletelink").href ="index.php?p=category&action=3&img="+img+"&cateid="+cateid;
        }
        function loadDataForEdit(cateid){
            var name = _("name-"+cateid).getAttribute("data-value");
            _("editcateName").value = name;
            var desc = _("desc-"+cateid).getAttribute("data-value");
            _("editcateDesc").value = desc;
            
            var img = _("img-"+cateid).getAttribute("data-value");
            _("oldimg").src="../images/categories/thumbnail/"+img;
            _("oldimgname").innerHTML=img;
            _("cateditform").action = _("cateditform").action + "&cateid=" + cateid ;
            _("txtoldimg").value = img;
        }
    </script>