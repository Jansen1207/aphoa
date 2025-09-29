<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      background-image: url('./images/bgaphoa.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
    }

   .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 100px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

   .terms-and-conditions {
      text-align: center;
    }

   .terms-and-conditions h2 {
      margin-top: 0;
    }

   .terms-and-conditions p {
      margin-bottom: 20px;
    }

   .agreement {
      margin-top: 20px;
    }

   .agreement input[type="radio"] {
      margin: 0 10px 0 0;
    }

   .agreement label {
      font-weight: bold;
      color: #333;
    }

   .submit-button {
      margin-top: 30px;
    }

   .submit-button button[type="submit"] {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

   .submit-button button[type="submit"]:hover {
      background-color: #3e8e41;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="terms-and-conditions">
      <h2>HOMEOWNERS CONSENT FORM FOR THE PROCESSING, RELEASE AND RETENTION OF PERSONAL INFORMATION</h2>
      <p>I am fully aware, that ANAK-PAWIS HOMEOWNERS ASSOCIATION (APHOA),INC. or its designated representative is duty bound and obligated under the Data Privacy Act of 2012 to protect all my personal and sensitive information that it collects, processes, and retains upon my homeowners membership and during my stay in Anak-Pawis, Brgy. San Juan Cainta, Rizal.</p>
      <p>Homeowners personal information includes any information about my identity, or any documents containing my identity. This includes but not limited to my name, address, date of birth, family members, occupation, email address, contact numbers, educational attainment, civil status homeowner status and other information necessary for basic administration and instruction.</p>
      <p>I understand that my personal information cannot be disclosed without my consent. I understand that the information that was collected and processed relates to my membership and to be used by APHOA to pursue its legitimate interests as homeowner’s association. Likewise, I am fully aware that APHOA may share such information to affiliated or partner organizations as part of its contractual obligations, or with government agencies pursuant to law or legal processes. In this regard, I hereby allow APHOA to collect, process, use and share my personal data in the pursuit of its legitimate interests as a homeowner’s association.</p>
      <div class="agreement">
        <input type="radio" id="agree" name="agreement" value="agree">
        <label for="agree">I agree to the terms and conditions</label>
        <br>
        <input type="radio" id="disagree" name="agreement" value="disagree">
        <label for="disagree">I disagree to the terms and conditions</label>
      </div>
      <div class="submit-button">
        <button type="submit" onclick="checkAgreement()">Submit</button>
      </div>
    </div>
  </div>

  <script>
    function checkAgreement() {
      var agreement = document.querySelector('input[name="agreement"]:checked').value;
      if (agreement === 'disagree') {
        alert("Please note that you must agree to the terms and conditions including data privacy consent before you proceed.");
        return false;
      } else if (agreement === 'agree') {
        window.location.href = 'register.php';
      }
    }
  </script>
</body>
</html>