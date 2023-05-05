<!-- Content -->
<?php
    /*Actions on slideshow
       0.Enable
        1.Disable
        2.move up
        3.move down
        4.add new
        5.edit//no function
        6.update 
        7.delete
    */
    
    $error = -1; /*-1 : Normal, 1 : Error, 0 : Succcess*/
    if(isset($_GET['action'])){
    $action=$_GET['action'];
    switch($action){
        case "0":
            $ssid=$_GET['ssid'];
            $sql ="UPDATE tbslideshow set status='1' WHERE ssId=$ssid";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            break;
        case "1":
            $ssid=$_GET['ssid'];
            $sql ="UPDATE tbslideshow set status='0' WHERE ssId=$ssid";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            break;
        case "2":
            $cur_ssid=$_GET['ssid'];
            $cur_ssorder=$_GET['ssorder'];
            $sql="SELECT ssOrder,ssId FROM tbslideshow WHERE ssOrder < $cur_ssorder ORDER BY ssOrder desc limit 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            if($stmt->rowCount() > 0 ){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                foreach($stmt->fetchAll() as $row){
                    $next_ssid=$row['ssId'];
                    $next_ssorder=$row['ssOrder'];
                    $sql ="UPDATE tbslideshow SET ssOrder=$cur_ssorder WHERE ssId=$next_ssid";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $sql ="UPDATE tbslideshow SET ssOrder=$next_ssorder WHERE ssId=$cur_ssid";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                } 
                
            }
            break;
        case "3":
            $cur_ssid=$_GET['ssid'];
            $cur_ssorder=$_GET['ssorder'];
            $sql="SELECT ssOrder,ssId FROM tbslideshow WHERE ssOrder > $cur_ssorder ORDER BY ssOrder asc limit 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            if($stmt->rowCount() > 0 ){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                foreach($stmt->fetchAll() as $row){
                    $next_ssid=$row['ssId'];
                    $next_ssorder=$row['ssOrder'];
                    $sql ="UPDATE tbslideshow SET ssOrder=$cur_ssorder WHERE ssId=$next_ssid";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $sql ="UPDATE tbslideshow SET ssOrder=$next_ssorder WHERE ssId=$cur_ssid";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                } 
                
            }
            break;
        case "4":
            $title =$_POST['ssTitle'];
            $subtitle =$_POST['ssSubtile'];
            $enable =0;
            if(isset($_POST['chkenable'])){
                $enable = 1;
            }
            $sql = "SELECT MAX(ssOrder)+1 as newssorder FROM tbslideshow limit 1";
            $stmt =$conn->prepare($sql);
            $stmt->execute();
            $ssorder = 1;
            if($stmt->rowCount()>=1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $ssorder = $row['newssorder'];
            }

            $img ="noimage.jpg";
            if(file_exists(isset($_FILES['ssImg']['tmp_name'])) || is_uploaded_file($_FILES['ssImg']['tmp_name']))
            {
                if(($_FILES['ssImg']['size'])/(1048576) <=3 )
                {
                    $ext = pathinfo($_FILES['ssImg']['name'], PATHINFO_EXTENSION);
                    if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                        $img = floor(microtime(true)*1000) . "." . $ext;
                        $tmp_name =$_FILES["ssImg"]["tmp_name"];
                        $sourceProperties = getimagesize($tmp_name);
                        $width = $sourceProperties[0];
                        $height = $sourceProperties[1];
                        $tarwidth = 200;
                        $tarheight = 150;
                        $imageType = $sourceProperties[2];
                        $destination  = "../images/slideshows";
                        createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                        move_uploaded_file($tmp_name,$destination."/".$img);
                        
                        $sql = "INSERT INTO tbslideshoW(ssTitle, ssSubtile, ssImg, ssOrder, status) VALUES('$title','$subtitle','$img',$ssorder,'$enable')";
                        if($conn->exec($sql)){
                            $error = 0;
                            $errmsg = "A slideshow has been added successfully!";
                        }else{
                            $error = 1;
                            $errmsg = "Fail to add the slideshow!";
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
        case "6":
            $ssid = $_GET['ssid'];
            $title = $_POST['edittitle'];
            $subtitle = $_POST['editsubtitle'];
            $enable ="0";
            if(isset($_POST['editchkenable']))
            {
                $enable = 1;
            }else{
                $enable = 0;
            }
            if($title != "")
            {
                if(file_exists(isset($_FILES['editslide_img']['tmp_name'])) || is_uploaded_file($_FILES['editslide_img']['tmp_name']))
                {
                    if(($_FILES['editslide_img']['size'])/(1048576) <=3 )
                    {
                        $ext = pathinfo($_FILES['editslide_img']['name'], PATHINFO_EXTENSION);
                        if($ext =="jpeg" || $ext =="jpg" || $ext =="gif" || $ext =="png"){
                            $img = floor(microtime(true)*1000) . "." . $ext;
                            $tmp_name =$_FILES["editslide_img"]["tmp_name"];
                            $sourceProperties = getimagesize($tmp_name);
                            $width = $sourceProperties[0];
                            $height = $sourceProperties[1];
                            $tarwidth = 200;
                            $tarheight = 150;
                            $imageType = $sourceProperties[2];
                            $destination  = "../images/slideshows";
                            createThumbnail($imageType,$tmp_name,$width,$height,$tarwidth,$tarheight,$destination,$img);
                            move_uploaded_file($tmp_name,$destination."/".$img);
                            
                            $sql ="UPDATE tbslideshow SET ssTitle='$title',ssSubtile = '$subtitle', ssImg = '$img', status = '$enable' WHERE  ssId = $ssid ";
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
                                $errmsg = "A slideshow has been updated successfully!";
                            }else{
                                $error = 1;
                                $errmsg = "Fail to update the slideshow!";
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
                    $sql ="UPDATE tbslideshow SET ssTitle='$title',ssSubtile = '$subtitle', status = '$enable' WHERE  ssId = $ssid ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    if($stmt->rowCount()>0){
                        $error = 0;
                        $errmsg = "A slideshow has been updated successfully!";
                    }else{
                        $error = 1;
                        $errmsg = "Fail to update the slideshow!";
                    }
                }
                
            }
            break;
        case "7":
            $img=$_GET['img'];
            $ssid=$_GET['ssid'];
            $sql ="DELETE FROM tbslideshow WHERE ssId=$ssid";
            if($conn->exec($sql)){
                $imgname ="../images/slideshows/$img";
                $imgthumbnail ="../images/slideshows/thumbnail/$img";
                if(file_exists($imgname)){
                    unlink($imgname);
                }
                if(file_exists($imgthumbnail)){
                    unlink($imgthumbnail);
                }
                $error = 0;
                $errmsg="A slideshow has been deleted successfully!";
                
            }else{
                $error = 1;
                $errmsg = "Fail to delete a slideshow!";
            }
            break;
    }
   }
    $sql="SELECT * FROM  tbslideshow ORDER BY ssOrder ASC";
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

    $sql="SELECT * FROM  tbslideshow ORDER BY ssOrder ASC limit ". MAXPERPAGE." offset $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
?>
    <!-- Code For Slideshow -->
        <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                <div class="row">
                    <div class="col-6">
                        <h5 class="card-header">Slideshow</h5>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <button
                            type="button"
                            class="btn btn-success fw-bold text-center my-auto me-4"
                            data-bs-toggle="modal"
                            data-bs-target="#create_slideshow"
                            >
                            <i class='menu-icon bx bxs-message-square-add me-2'></i>Create Slideshow
                        </button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <!-- alert Messages -->
                    <?php 
                        if($error != -1){
                    ?>
                        <div class="alert alert-<?= ($error == 1?"danger":"success")?> alert-dismissible" role="alert">
                            <?= $errmsg?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                    <?php
                        }
                    ?>
                    <!-- End of alert Messages -->
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Sub Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="silde_table">
                            <?php 
                                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                $i=$offset+1;
                                foreach($stmt->fetchAll()as $row){
                            ?>
                            <tr>
                                <td id="ssid-<?=$row['ssId']?>"  data-value="<?=$row['ssId']?>" ><?= $i++ ?></td>
                                <td id="img-<?=$row['ssId']?>" data-value="<?=$row['ssImg']?>"><img src="../images/slideshows/thumbnail/<?= $row["ssImg"]?>" style="width:150px;height:80px;" alt="Avatar"/></td>
                                <td id="title-<?=$row['ssId']?>" data-value="<?=$row['ssTitle'] ?>"><strong><?= $row["ssTitle"]?></strong></td>
                                <td id="subtitle-<?=$row['ssId']?>" data-value="<?=$row['ssSubtile'] ?>"><?= $row["ssSubtile"]?></td>
                                <td style="font-size: 20px;">
                                    <a href="index.php?p=slideshow&action=<?= $row['status']?>&ssid=<?= $row["ssId"]?>&pg=<?=$pg?>" id="enable-<?=$row['ssId']?>" data-value="<?=$row['status'] ?>" title="Enable & Disable"><i class="fa-regular <?= $row["status"]?"fa-eye":"fa-eye-slash"?>"></i></a>
                                    <a href="index.php?p=slideshow&action=2&ssorder=<?= $row['ssOrder']?>&ssid=<?= $row['ssId']?>&pg=<?=$pg?>" title="Move UP"><i class='bx bx-up-arrow-alt'></i></a>
                                    <a href="index.php?p=slideshow&action=3&ssorder=<?= $row['ssOrder']?>&ssid=<?= $row['ssId']?>&pg=<?=$pg?>" title="Move Down"><i class='bx bx-down-arrow-alt' ></i></a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#update_slideshow" onclick="loadDataForEdit('<?= $row['ssId']?>')" title="Edit"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="updateDeleteLink('<?= $row['ssImg']?>','<?= $row['ssId']?>','<?=$pg?>')" title="Delete"><i class="bx bx-trash me-1"></i></a>
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
    <!-- Code For Slideshow -->

<!-- / Content -->

    <!-- Add Modal -->
            <div class="modal fade" id="create_slideshow" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Create Slideshow</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                    </div>
                    <div class="modal-body">

                        <!-- Form -->
                        <form action="index.php?p=slideshow&action=4" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Title -->
                                <div class="col mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input
                                        type="text"
                                        id="title"
                                        class="form-control"
                                        placeholder="Slide Title"
                                        name="ssTitle"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- Subtitle -->
                            <div class="row">
                                <div class="col mb-3">
                                <label for="subtitle" class="form-label">SubTitle</label>
                                <input
                                    type="text"
                                    id="subtitle"
                                    class="form-control"
                                    placeholder="Slide SubTitle"
                                    name="ssSubtile"
                                    required
                                />
                                </div>
                            </div>
                            <!-- checkbox -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="chkenable" name="chkenable" checked />
                                <label class="form-check-label" for="chkenable">Enable</label>
                            </div>
                            <!-- images -->
                            <div class="row">
                                <div class="col mb-3">
                                <label for="slide_img" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Slide Image</label>
                                <input type="file" name="ssImg" id="slide_img" class="form-control">
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-secondary float-end "data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary me-3 float-end">Create</button>
                        </form>
                    </div>
                    </div>
                
                </div>
            </div>
            

    <!-- End Add Slideshow Modal -->
    
    <!-- Update Modal -->
        <div class="modal fade" id="update_slideshow" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update the Slideshow</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                    </div>
                    <div class="modal-body">

                        <!-- Form -->
                        <form action="index.php?p=slideshow&action=6" id="sseditform" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Title -->
                                <div class="col mb-3">
                                    <label for="edittitle" class="form-label">Title</label>
                                    <input
                                        type="text"
                                        id="edittitle"
                                        class="form-control"
                                        placeholder="Slide Title"
                                        name="edittitle"
                                        required
                                    />
                                </div>
                            </div>
                            <!-- Subtitle -->
                            <div class="row">
                                <div class="col mb-3">
                                <label for="editsubtitle" class="form-label">SubTitle</label>
                                <input
                                    type="text"
                                    id="editsubtitle"
                                    class="form-control"
                                    placeholder="Slide SubTitle"
                                    name="editsubtitle"
                                    required
                                />
                            </div>
                            </div>
                            <!-- checkbox -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="editchkenable" name="editchkenable" checked />
                                <label class="form-check-label" for="editchkenable">Enable</label>
                            </div>
                            <!-- images -->
                            <div class="row">
                                <div class="col mb-3">
                                <label for="editslide_img" class="form-label btn btn-primary px-3 py-2 d-flex justify-content-center w-50 mx-auto mb-3"><i class='menu-icon bx bx-image-add me-2'></i>Upload Slide Image</label>
                                <input type="file" name="editslide_img" id="editslide_img" class="form-control">
                                </div>
                            </div>
                            <!-- show image -->
                            <div class="row">
                                <div class="col mb-3">
                                    <img src="#" id="oldimg" style="width: 150px; height:80px; "  />
                                    <p id="oldimgname" ></p>
                                </div>
                            </div>
                            <!-- hidden old image name -->
                            <input type="hidden" value="" id="txtoldimg" name="txtoldimg">

                            <button type="button" class="btn btn-secondary float-end "data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary me-3 float-end">Update</button>
                        </form>
                    </div>
                    </div>
                
                </div>
            </div>
    <!-- End Update Modal -->

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
        function updateDeleteLink(img,ssid,pg){
            _("deletelink").href ="index.php?p=slideshow&action=7&img="+img+"&ssid="+ssid;
        }
        function loadDataForEdit(ssid){
            var title = _("title-"+ssid).getAttribute("data-value");
            _("edittitle").value = title;
            var subtitle = _("subtitle-"+ssid).getAttribute("data-value");
            _("editsubtitle").value = subtitle;
            var enable = _("enable-"+ssid).getAttribute("data-value");
            if(enable == "1" )
            {
                _("editchkenable").checked = true;
            }else{
                _("editchkenable").checked = false;
            }
            var img = _("img-"+ssid).getAttribute("data-value");
            _("oldimg").src="../images/slideshows/thumbnail/"+img;
            _("oldimgname").innerHTML=img;
            _("sseditform").action = _("sseditform").action + "&ssid=" + ssid ;
            _("txtoldimg").value = img;
        }
    </script>
