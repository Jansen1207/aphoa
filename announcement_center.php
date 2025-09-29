<?php
require('config.php');
session_start();
$sql = "SELECT m.*, i.* FROM members m 
        INNER JOIN information i ON m.id = i.member_id
        WHERE m.id = '{$_SESSION['member_id']}'";
$result = $conn->query($sql);
$memberData = $result->fetch_assoc();
$announcementsSql = "SELECT id, title, message, created_at FROM announcements ORDER BY created_at DESC";
$announcementsResult = $conn->query($announcementsSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Center</title>
    <link rel="stylesheet" href="./css/member.css">
    <link rel="stylesheet" href="./css/announcement.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    
<div class="header">
    <table>
        <tr>
            <td>
                <h1 style="margin-bottom:1px;margin-top:1px;">Announcement Center</h1>
            </td>
            <td align="right">
                <img src="./images/menicon.png" width="50" height="50" style="border-radius: 50%;">
            </td>
            <td width="120">
                &nbsp;&nbsp;&nbsp;
                <form action="logout.php" method="POST">
                    <select name="logout" onchange="this.form.submit()">
                        <option><?php echo ucfirst($memberData['first_name']) . ' ' . ucfirst($memberData['last_name']) ?></option>
                        <option style="background-color: #337AB7;color:#fff;" value="logout">Logout</option>
                    </select>
                </form>
            </td>
        </tr>
    </table>
</div>
<?php include './includes/officer_sidebar.php'; ?>
<div id="announcementcenter" style="display: block; font-family: 'Arial', sans-serif; background-color: #fff; color: #000; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
    <h3 style="text-align: center; font-weight: bold; margin-bottom: 20px; letter-spacing: 1px; color: #000;">Announcement Center</h3>
    <div id="message" style="color: #000; text-align: center; font-weight: bold; margin-bottom: 20px;"></div>
    <form id="announcementForm" action="./includes/announcement.php" method="post" style="display: flex; flex-direction: column; gap: 15px;">
        <input type="text" name="title" placeholder="Title" required aria-label="Title" 
            style="padding: 10px; border: none; border-radius: 5px; background-color: #fff; color: #000; font-size: 1rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
        <textarea name="message" placeholder="Message" required aria-label="Message" 
            style="padding: 10px; border: none; border-radius: 5px; background-color: #fff; color: #000; font-size: 1rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); min-height: 100px;"></textarea>
        <button type="submit" 
            style="padding: 12px 20px; border: none; border-radius: 30px; background: linear-gradient(90deg, #00c6ff, #0072ff); color: #fff; font-weight: bold; font-size: 1rem; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); text-transform: uppercase;">
            Submit
        </button>
    </form>
    <h4 style="margin-top: 30px; font-weight: bold; text-align: center; letter-spacing: 1px; color: #000;">Recent Announcements</h4>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; background-color: rgba(255, 255, 255, 0.1); border-radius: 10px; overflow: hidden; color: #000;">
        <thead>
            <tr style="background-color: rgba(0, 0, 0, 0.1);">
                <th style="padding: 15px; border: none; text-align: left; color: #000;">Title</th>
                <th style="padding: 15px; border: none; text-align: left; color: #000;">Message</th>
                <th style="padding: 15px; border: none; text-align: left; color: #000;">Created At</th>
                <th style="padding: 15px; border: none; text-align: center; color: #000;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($announcementsResult->num_rows > 0): ?>
                <?php while ($announcement = $announcementsResult->fetch_assoc()): ?>
                    <tr style="background-color: rgba(255, 255, 255, 0.1);">
                        <td style="padding: 15px; border: none; color: #000;"><?php echo htmlspecialchars($announcement['title']); ?></td>
                        <td style="padding: 15px; border: none; color: #000;"><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></td>
                        <td style="padding: 15px; border: none; color: #000;"><?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></td>
                        <td style="padding: 15px; border: none; text-align: center; color: #000;">
                            <div class="announcement" data-id="<?= $announcement['id'] ?>">
    <!-- Edit Button -->
    <button type="button" class="edit-inline-button"
        style="padding: 12px 24px; background: linear-gradient(135deg, #32cd32, #00ff00); color: #fff; border: none; border-radius: 25px; font-weight: bold; font-size: 1rem; cursor: pointer; text-transform: uppercase; box-shadow: 0 5px 15px rgba(50, 205, 50, 0.5); transition: transform 0.3s, box-shadow 0.3s;">
        Edit
    </button>

    <!-- Notify Button -->
    <button type="button" class="notify-button"
        style="padding: 12px 24px; background: linear-gradient(135deg, #007BFF, #00aaff); color: #fff; border: none; border-radius: 25px; font-weight: bold; font-size: 1rem; cursor: pointer; text-transform: uppercase; box-shadow: 0 5px 15px rgba(0, 123, 255, 0.5); transition: transform 0.3s, box-shadow 0.3s;"
        data-title="<?php echo htmlspecialchars($announcement['title']); ?>" 
        data-message="<?php echo htmlspecialchars($announcement['message']); ?>" 
        data-number="09989321472">
        Send Notifications
    </button>

    <!-- Delete Button -->
    <form action="./includes/delete_announcement.php" method="post" style="display: inline;">
        <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
        <button type="submit" 
            style="padding: 12px 24px; background: linear-gradient(135deg, #ff0000, #ff6347); color: #fff; border: none; border-radius: 25px; font-weight: bold; font-size: 1rem; cursor: pointer; text-transform: uppercase; box-shadow: 0 5px 15px rgba(255, 0, 0, 0.5); transition: transform 0.3s, box-shadow 0.3s;"
            onclick="return confirm('Are you sure you want to delete this announcement?');">
            Delete
        </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; font-weight: bold;">No announcements found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>







<script>
const _0x55ea7b=_0x5e4d;(function(_0x296b00,_0x4b337d){const _0x8add33=_0x5e4d,_0x2ad1d9=_0x296b00();while(!![]){try{const _0x53e1b4=parseInt(_0x8add33(0x23a))/(-0x2*-0x89+-0x3ef*0x5+0x129a)+-parseInt(_0x8add33(0x224))/(0x5b0*0x4+0x1c5+0x1*-0x1883)*(-parseInt(_0x8add33(0x226))/(-0xdfd*0x1+0x3*0x54f+-0x1ed))+parseInt(_0x8add33(0x231))/(0x1*-0x8a9+-0x530*-0x2+-0x1b3)*(parseInt(_0x8add33(0x1f4))/(0x1087+0x154e+0x10*-0x25d))+parseInt(_0x8add33(0x210))/(0x2368+0x301+-0x1f*0x13d)*(-parseInt(_0x8add33(0x1e2))/(-0x65d*0x1+0x1*0x1849+-0x11e5))+-parseInt(_0x8add33(0x1dc))/(-0x3*-0xa55+-0x1*0x734+0x4d*-0x4f)+parseInt(_0x8add33(0x22a))/(-0x27c+-0x1396+0x161b)*(-parseInt(_0x8add33(0x1f0))/(0x1920+0x150e+-0xb89*0x4))+-parseInt(_0x8add33(0x1f9))/(0x31*0x29+-0xb2f+0x361);if(_0x53e1b4===_0x4b337d)break;else _0x2ad1d9['push'](_0x2ad1d9['shift']());}catch(_0x14be46){_0x2ad1d9['push'](_0x2ad1d9['shift']());}}}(_0x40e8,-0x16350a+0x3d*-0x192f+0xa8085*0x4));function _0x40e8(){const _0x58c315=['ully!','RXUef','addEventLi','2758764INiaGK','TkAeW','click','sZFLr','t\x20successf','maphore.co','url','Thesis','LovtH','ing\x20SMS:\x20','yevPs','IVMib','THYDj','append','tton','a\x20notifica','Error\x20send','re\x20you\x20wan','Message:\x20','torAll','2580818QJdOXI','834e35c094','3RAxUds','t\x20to\x20send\x20','AlZLc','RFCRr','36OkuaOz','JFwDw','then','nFXRd','MvdSR','sendername','https://se','404pNrOms','XokNF','log','Title:\x20','success','POST','Error:','An\x20error\x20o','status','644405OWWVnb','message','11208728wNvfqP','apikey','ssages','OyHMn','forEach','data-title','7BGQYdX','getAttribu','dIebh','title','caMiB','195c8c2445','tion\x20to\x20al','stener','ccurred\x20wh','data-numbe','rioxi','CHbpH','data-messa','OlBoV','441460obeedR','error','catch','json','61475RGaAFI','DRPjL','pqkmc','g\x20the\x20SMS.','querySelec','2582063JyHAaq','EBZqt','65c050d0a4','on\x20SMS\x20sen','Notificati','ile\x20sendin','rLmPT','HFLOm','number','EXoqC','/api/v4/me','EkGLL','iVGYK','Flovt','IVkAn','Number:\x20','Are\x20you\x20su','tfTPF','send_sms.p','.notify-bu'];_0x40e8=function(){return _0x58c315;};return _0x40e8();}function sendSMSReminder(_0xb687c0){const _0x3fce6e=_0x5e4d,_0x546aff={'AlZLc':function(_0x44bee5,_0x1c3e3e){return _0x44bee5===_0x1c3e3e;},'rioxi':_0x3fce6e(0x235),'Flovt':function(_0x39d456,_0x4fc966){return _0x39d456(_0x4fc966);},'nFXRd':_0x3fce6e(0x1fd)+_0x3fce6e(0x1fc)+_0x3fce6e(0x214)+_0x3fce6e(0x20d),'MvdSR':function(_0x34c6f5,_0x558e0a){return _0x34c6f5(_0x558e0a);},'dIebh':function(_0x1e6715,_0x3cec18){return _0x1e6715+_0x3cec18;},'yevPs':_0x3fce6e(0x220)+_0x3fce6e(0x219),'OyHMn':_0x3fce6e(0x237),'CHbpH':function(_0x102e62,_0x44b544){return _0x102e62(_0x44b544);},'IVMib':_0x3fce6e(0x238)+_0x3fce6e(0x1ea)+_0x3fce6e(0x1fe)+_0x3fce6e(0x1f7),'HFLOm':_0x3fce6e(0x1e1),'RFCRr':_0x3fce6e(0x1ee)+'ge','XokNF':_0x3fce6e(0x1eb)+'r','sZFLr':_0x3fce6e(0x234),'DRPjL':_0x3fce6e(0x222),'caMiB':_0x3fce6e(0x208),'IVkAn':_0x3fce6e(0x1e7)+_0x3fce6e(0x1fb)+_0x3fce6e(0x225)+'09','tfTPF':_0x3fce6e(0x217),'EBZqt':_0x3fce6e(0x230)+_0x3fce6e(0x215)+_0x3fce6e(0x203)+_0x3fce6e(0x1de),'RXUef':_0x3fce6e(0x1e5),'rLmPT':_0x3fce6e(0x1db),'TkAeW':_0x3fce6e(0x201),'JFwDw':_0x3fce6e(0x22f),'THYDj':_0x3fce6e(0x1dd),'iVGYK':_0x3fce6e(0x216),'EXoqC':function(_0x1c91ea,_0x5435e8,_0x348102){return _0x1c91ea(_0x5435e8,_0x348102);},'LovtH':_0x3fce6e(0x20b)+'hp','EkGLL':_0x3fce6e(0x236)},_0x5bb3e5=_0xb687c0[_0x3fce6e(0x1e3)+'te'](_0x546aff[_0x3fce6e(0x200)]),_0x578d54=_0xb687c0[_0x3fce6e(0x1e3)+'te'](_0x546aff[_0x3fce6e(0x229)]),_0x31edfa=_0xb687c0[_0x3fce6e(0x1e3)+'te'](_0x546aff[_0x3fce6e(0x232)]);console[_0x3fce6e(0x233)](_0x546aff[_0x3fce6e(0x213)],_0x5bb3e5),console[_0x3fce6e(0x233)](_0x546aff[_0x3fce6e(0x1f5)],_0x578d54),console[_0x3fce6e(0x233)](_0x546aff[_0x3fce6e(0x1e6)],_0x31edfa);const _0x1a9e59=_0x546aff[_0x3fce6e(0x207)],_0xd1280e=_0x546aff[_0x3fce6e(0x20a)],_0x46b27f=_0x546aff[_0x3fce6e(0x1fa)];if(_0x546aff[_0x3fce6e(0x206)](confirm,_0x3fce6e(0x209)+_0x3fce6e(0x221)+_0x3fce6e(0x227)+_0x3fce6e(0x21f)+_0x3fce6e(0x1e8)+'l?')){const _0x5e0370=new FormData();_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x20e)],_0x5bb3e5),_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x1ff)],_0x578d54),_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x211)],_0x31edfa),_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x22b)],_0xd1280e),_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x21c)],_0x1a9e59),_0x5e0370[_0x3fce6e(0x21d)](_0x546aff[_0x3fce6e(0x205)],_0x46b27f),_0x546aff[_0x3fce6e(0x202)](fetch,_0x546aff[_0x3fce6e(0x218)],{'method':_0x546aff[_0x3fce6e(0x204)],'body':_0x5e0370})[_0x3fce6e(0x22c)](_0x5490b4=>_0x5490b4[_0x3fce6e(0x1f3)]())[_0x3fce6e(0x22c)](_0xc34329=>{const _0x102662=_0x3fce6e;_0x546aff[_0x102662(0x228)](_0xc34329[_0x102662(0x239)],_0x546aff[_0x102662(0x1ec)])?_0x546aff[_0x102662(0x206)](alert,_0x546aff[_0x102662(0x22d)]):_0x546aff[_0x102662(0x22e)](alert,_0x546aff[_0x102662(0x1e4)](_0x546aff[_0x102662(0x21a)],_0xc34329[_0x102662(0x1db)]));})[_0x3fce6e(0x1f2)](_0x1563b4=>{const _0x19f77f=_0x3fce6e;console[_0x19f77f(0x1f1)](_0x546aff[_0x19f77f(0x1df)],_0x1563b4),_0x546aff[_0x19f77f(0x1ed)](alert,_0x546aff[_0x19f77f(0x21b)]);});}}function _0x5e4d(_0x1b22ab,_0x4b3e7d){const _0x2293a2=_0x40e8();return _0x5e4d=function(_0x37f5a0,_0x127b5e){_0x37f5a0=_0x37f5a0-(0xa4*0x1+-0x162c+0x1763);let _0x2aa5ed=_0x2293a2[_0x37f5a0];return _0x2aa5ed;},_0x5e4d(_0x1b22ab,_0x4b3e7d);}document[_0x55ea7b(0x1f8)+_0x55ea7b(0x223)](_0x55ea7b(0x20c)+_0x55ea7b(0x21e))[_0x55ea7b(0x1e0)](_0x22194f=>{const _0x341e4c=_0x55ea7b,_0x42da47={'pqkmc':function(_0x312287,_0x17541f){return _0x312287(_0x17541f);},'OlBoV':_0x341e4c(0x212)};_0x22194f[_0x341e4c(0x20f)+_0x341e4c(0x1e9)](_0x42da47[_0x341e4c(0x1ef)],function(){const _0x493006=_0x341e4c;_0x42da47[_0x493006(0x1f6)](sendSMSReminder,this);});});
</script>






<script>



$(document).ready(function() {






    $(document).on('click', '.edit-inline-button', function() {
        const announcementDiv = $(this).parent();
        const announcementId = announcementDiv.data('id');
        const announcementText = announcementDiv.find('.announcement-text').text();
        const announcementTitle = announcementDiv.closest('tr').find('td:nth-child(1)').text(); 

        const textarea = $('<textarea style="width: 100%;"></textarea>').val(announcementText);
        const titleInput = $('<input type="text" style="width: 100%;" />').val(announcementTitle);
        const saveButton = $('<button class="save-button" style="margin-left: 5px; background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Save</button>');
        

        announcementDiv.find('.announcement-text').hide();
        $(this).hide(); 
        
  
        announcementDiv.append(titleInput).append(textarea).append(saveButton);


        saveButton.on('click', function() {
            const updatedText = textarea.val();
            const updatedTitle = titleInput.val();
            
            $.ajax({
                type: 'POST',
                url: '/includes/edit_announcement.php',
                data: { 
                    announcement_id: announcementId, 
                    announcement_text: updatedText,
                    title: updatedTitle 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Announcement updated successfully!');
                        location.reload(); 
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating announcement.');
                }
            });
        });
    });
});

</script>

</body>
</html>
