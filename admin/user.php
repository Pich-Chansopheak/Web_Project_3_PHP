<?php
    /*Actions on slideshow
        1.add new
        2.delete
        3.enable 
        4.disable
        5.active deactivate
    */
    $error = -1; /*-1 : Normal, 1 : Error, 0 : Succcess*/
    if(isset($_GET['action']))
    {
        $action=$_GET['action'];
        switch($action)
        {
            case "1":
                $name = $_POST['username'];
                $pwd =$_POST['password'];
                $fname = $_POST['fullname'];
                date_default_timezone_set('Asia/Phnom_Penh');
                $time = date('Y-m-d H:i:s');
                $email = $_POST['email'];
                $isadmin = 0;
                if(isset($_POST['chkadmin'])){
                    $isadmin = 1;
                }
                $active = 0;
                if(isset($_POST['chkactive'])){
                    $active = 1;
                }
                if($name!=""){
                    $sql = "INSERT INTO tbuser( username, password, fullname, isadmin, active,c,email) VALUES('$name','$pwd','$fname','$isadmin','$active','$time','$email')";
                    if($conn->exec($sql)){
                        $error = 0;
                        $errmsg = "A User has been added successfully!";
                    }else{
                        $error = 1;
                        $errmsg = "Fail to add the User!";
                    }
                }
                break;
            case "2":
                $userid=$_GET['userid'];
                $sql ="DELETE FROM tbuser WHERE userid=$userid";
                if($conn->exec($sql)){
                    $error = 0;
                    $errmsg="A user has been deleted successfully!";
                    
                }else{
                    $error = 1;
                    $errmsg = "Fail to delete the user!";
                }
                break;
            case "3":
                $userid=$_GET['userid'];
                $admin=$_GET['admin'];
                $isadmin = 1;
                if($admin =='1'){
                    $isadmin = 0;
                }
                $sql ="UPDATE tbuser set isadmin='$isadmin' WHERE userid=$userid";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                break;
            case "4":
                $userid=$_GET['userid'];
                $a =$_GET['active'];
                $active = 1;
                if($a =='1'){
                    $active = 0;
                }
                $sql ="UPDATE tbuser set active='$active' WHERE userid=$userid";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                break;
        }
    }
    $sql="SELECT * FROM  tbuser ";
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

    $sql="SELECT * FROM  tbuser ORDER BY userid ASC limit ". MAXPERPAGE." offset $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
?>
    <!-- Code For User -->
        <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                <div class="row">
                    <div class="col-6">
                        <h5 class="card-header">User</h5>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <button
                            type="button"
                            class="btn btn-success fw-bold text-center my-auto me-4"
                            data-bs-toggle="modal"
                            data-bs-target="#create_user"
                            >
                            <i class='menu-icon bx bxs-message-square-add me-2'></i>Create User
                        </button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <!-- alert Messages -->
                    <?php include "msgbox.php"?>
                    <!-- End of alert Messages -->
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Admin</th>
                                <th>Active</th>
                                <th>Last Login</th>
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
                                <td id="userid-<?=$row['userid']?>"  data-value="<?=$row['userid']?>" ><?= $i++ ?></td>
                                <td id="username-<?=$row['userid']?>" data-value="<?=$row['username'] ?>"><strong><?= $row["username"]?></strong></td>
                                <td id="fullname-<?=$row['userid']?>" data-value="<?=$row['fullname'] ?>"><?= $row["fullname"]?></td>
                                <td id="email-<?=$row['userid']?>" data-value="<?=$row['email'] ?>"><?= $row["email"]?></td>
                                <td id="isadmin-<?=$row['userid']?>" data-value="<?=$row['isadmin'] ?>"><i class="text-primary fa-solid fa-user-<?=$row["isadmin"]?"check":"xmark"?> fa-lg"></i></td>
                                <td id="active-<?=$row['userid']?>" data-value="<?=$row['active'] ?>"><i class="text-primary fa-solid fa-circle-<?=$row['active']?"check":"xmark"?> fa-lg"></i></td>
                                <td id="lastlog-<?=$row['userid']?>" data-value="<?=$row['lastlogin'] ?>"><?=$row['lastlogin']?></td>
                                <td style="font-size: 20px;">
                                    <a href="index.php?p=user&action=3&admin=<?= $row['isadmin']?>&userid=<?= $row["userid"]?>&pg=<?=$pg?>" id="isadmin-<?=$row['userid']?>" data-value="<?=$row['isadmin'] ?>" title="Enable & Disable Admin"><i class="fa-solid fa-user<?=$row['isadmin']?"":"-slash"?> fa-lg me-1"></i></a>
                                    <a href="index.php?p=user&action=4&active=<?= $row['active']?>&userid=<?= $row["userid"]?>&pg=<?=$pg?>" id="active-<?=$row['active']?>" data-value="<?=$row['active'] ?>" title="activate & Disactivate user"><i class="fa-solid fa-<?=$row['active']?"check":"x"?> fa-lg me-1"></i></a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="updateDeleteLink('<?= $row['userid']?>')" title="Delete"><i class="bx bx-trash bx-md me-1"></i></a>
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
    <!-- Code For User -->

    <!-- Add Modal -->
        <div class="modal fade" id="create_user" tabindex="-1" aria-hidden="true">
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
                    <form action="index.php?p=user&action=1" method="post" enctype="multipart/form-data">
                        <!-- Title -->
                        <div class="row">
                            <div class="col mb-3">
                                <label for="title" class="form-label">Username</label>
                                <input
                                    type="text"
                                    id="title"
                                    class="form-control"
                                    placeholder="Enter Username"
                                    name="username"
                                    required
                                />
                            </div>
                        </div>
                        <!-- Full name -->
                        <div class="row">
                            <div class="col mb-3">
                            <label for="subtitle" class="form-label">Full Name</label>
                            <input
                                type="text"
                                id="subtitle"
                                class="form-control"
                                placeholder="Enter Full Name"
                                name="fullname"
                                required
                            />
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="row">
                            <div class="col mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="email"
                                class="form-control"
                                placeholder="example@gmail.com"
                                name="email"
                                required
                            />
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="row">
                            <div class="col mb-3">
                            <label for="subtitle" class="form-label">Password</label>
                            <input
                                type="password"
                                id="password"
                                class="form-control"
                                placeholder="Enter password"
                                name="password"
                                required
                            />
                            </div>
                        </div>
                        
                        
                        <!-- checkbox admin -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="chkadmin" name="chkadmin" checked />
                            <label class="form-check-label" for="chkadmin">Admin</label>
                        </div>
                        <!-- checkbox active -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="chkactive" name="chkactive" checked />
                            <label class="form-check-label" for="chkactive">Active</label>
                        </div>
                        
                        <button type="button" class="btn btn-secondary float-end "data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary me-3 float-end">Create</button>
                    </form>
                </div>
                </div>
            
            </div>
        </div>
            

    <!-- End Add Slideshow Modal -->
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
        function updateDeleteLink(userid){
            _("deletelink").href ="index.php?p=user&action=2&userid="+userid;
        }
    </script>
