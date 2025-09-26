<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .details { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>www.tariahealth.com</h2>
        <p>FCB Mihrab • Mezzanine №2<br>Lenana Road • Kilimani • NRB<br>Phone: 0706 487 965</p>
    </div>

    <h3>INDIVIDUAL WELLNESS REPORT</h3>

    <div class="section">
        <p><strong>Blood Pressure:</strong> {{ $vital ? $vital->bp_systolic . '/' . $vital->bp_diastolic . ' mmHg' : 'N/A' }}</p>
        <p><strong>Pulse:</strong> {{ $vital ? $vital->pulse . ' bpm' : 'N/A' }}</p>
        <p><strong>Temperature:</strong> {{ $vital ? $vital->temp . ' °C' : 'N/A' }}</p>
        <p><strong>Weight:</strong> {{ $nutrition ? $nutrition->weight . ' kgs' : 'N/A' }}</p>
        <p><strong>Height:</strong> {{ $nutrition ? $nutrition->height . ' cm' : 'N/A' }}</p>
        <p><strong>{{ $registration->first_name }} {{ $registration->surname }}</strong> {{ $registration->email }}</p>
    </div>

    <div class="section">
        <p><strong>BMI:</strong> {{ $nutrition ? $nutrition->bmi : 'N/A' }}</p>
        <p><strong>Blood Sugar:</strong> {{ $vital ? $vital->rbs . ' mg/dL' : 'N/A' }}</p>
        <p><strong>Body Fat Percentage:</strong> {{ $nutrition ? $nutrition->body_fat_percent . '%' : 'N/A' }}</p>
    </div>

    <div class="section">
        <h4>Screening Results</h4>
        <div class="details">
            <!-- Add specific screening results if available in the future -->
            <p>[Screening data to be implemented]</p>
        </div>
    </div>

    <div class="section">
        <h4>Discussion Summary</h4>
        <div class="details">
            <p>{{ $nutrition ? $nutrition->notes_nutritionist : 'No notes available' }}</p>
        </div>
    </div>

    <div class="section">
        <h4>Personalized Health Goal</h4>
        <div class="details">
            <p>Steady daily movement to increase the level of physical activity</p>
            <p>Aim to lose {{ $nutrition ? round($nutrition->weight - 10, 1) . ' kgs' : '10 kgs' }} over the next 12 months and maintain the habit change to sustain a healthy body weight</p>
            <p>Walk briskly for at least 45 minutes to one hour every other day</p>
            <p>Gradually increase the amount of vegetables in each meal to feel fuller and reduce your carbohydrate intake</p>
            <p><strong>Target:</strong> Goal weight in Sep ’26 is {{ $nutrition ? round($nutrition->weight - 10, 1) : 'N/A' }} kgs</p>
            <p><strong>Healthy weight for height range (kgs):</strong> {{ $nutrition ? $nutrition->lower_limit_weight . ' kgs - ' . $nutrition->weight_limit_weight . ' kgs' : 'N/A' }}</p>
            <p><strong>Healthy Body Fat % in Men 18-24%:</strong> [To be adjusted based on gender/age]</p>
        </div>
    </div>

    <div class="section">
        <p><strong>Dr. Mymoona Mohammed</strong></p>
        <p>{{ $currentDate }}</p>
    </div>
</body>
</html>