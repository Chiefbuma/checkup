<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wellness Report - {{ isset($record) ? $record->first_name . ' ' . $record->surname : 'Unknown Patient' }}</title>
    <style>
        body {
            font-family: "Calibri", sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 50%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .header .date {
            text-align: right;
            margin-top: 5px;
            font-size: 14px;
            font-family: "Calibri", sans-serif;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 10px 0;
            font-size: 15px;
            text-decoration: underline;
            color: blue;
            font-family: "Calibri", sans-serif;
        }
        .section-content {
            margin-bottom: 15px;
        }
        .section-content p {
            margin: 5px 0;
            font-size: 14px;
            font-family: "Calibri", sans-serif;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #000000;
            font-size: 12px;
        }
        .footer p {
            margin: 2px 0;
            font-family: "Calibri", sans-serif;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <img src="http://127.0.0.1:8000/images/wide-logo.png" alt="Wide Logo" class="logo">
            <div class="date">Friday, 26th September 2025</div>
        </div>

        <div class="section-title">INDIVIDUAL WELLNESS REPORT:</div>
        <div class="section-content">
            <p><strong>{{ isset($record) ? $record->first_name . ' ' . $record->surname : 'Unknown Patient' }}</strong> {{ isset($record) ? $record->email : 'N/A' }}</p>
        </div>

        <div class="section-title">Screening Results</div>
        <div class="section-content">
            <p><strong>Blood Pressure:</strong> {{ $vital ? $vital->bp_systolic . '/' . $vital->bp_diastolic . ' mmHg' : 'N/A' }}</p>
            <p><strong>Pulse:</strong> {{ $vital ? $vital->pulse . ' bpm' : 'N/A' }}</p>
            <p><strong>Temperature:</strong> {{ $vital ? $vital->temp : 'N/A' }}</p>
            <p><strong>Weight:</strong> {{ $nutrition ? $nutrition->weight . ' kgs' : 'N/A' }}</p>
            <p><strong>Height:</strong> {{ $nutrition ? $nutrition->height . ' cm' : 'N/A' }}</p>
            <p><strong>BMI:</strong> {{ $nutrition ? number_format($nutrition->bmi, 1) : 'N/A' }}</p>
            <p><strong>Blood sugar:</strong> {{ $vital ? $vital->rbs . ' mg/dL' : 'N/A' }}</p>
            <p><strong>Body fat percentage:</strong> {{ $nutrition ? $nutrition->body_fat_percent . '%' : 'N/A' }}</p>
            <p><strong>Healthy weight for height range (kgs):</strong> {{ $nutrition ? number_format($nutrition->lower_limit_weight, 1) . 'kgs - ' . number_format($nutrition->weight_limit_weight, 1) . 'kgs' : 'N/A' }}</p>
            <p><strong>Healthy Body fat % in Men 18 -24%</strong></p>
        </div>

        <div class="section-title">Discussion Summary</div>
        <div class="section-content">
            <p>Steady daily movement to increase the level of physical activity</p>
            <p>Aim to lose {{ $nutrition ? number_format($nutrition->weight - 10, 1) . 'kgs' : '10kgs' }} over the next 12 months and maintain the habit change to sustain a healthy body weight</p>
        </div>

        <div class="section-title">Personalized Health Goal</div>
        <div class="section-content">
            <p>Walk briskly for at least 45 minutes to one hour every other day</p>
            <p>Gradually increase the amount of vegetables in each meal to feel fuller and reduce your carbohydrate intake</p>
            <p><strong>Target:</strong> Goal weight in Sep ’26 is {{ $nutrition ? number_format($nutrition->weight - 10, 1) . 'kgs' : 'N/A' }}</p>
        </div>

        <div class="section-title">Dr. Mymoona Mohammed</div>
        <div class="footer">
            <p>FCB Mihrab • Mezzanine №2</p>
            <p>Lenana Road • Kilimani • NRB</p>
            <p>www.tariahealth.com</p>
            <p>Phone: 0706 487 965</p>
        </div>
    </div>
</body>
</html>