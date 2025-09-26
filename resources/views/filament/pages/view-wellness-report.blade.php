<div>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000000;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .report-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a3c6d;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .borrower-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .section-title {
            background: #edf2f7;
            padding: 8px 15px;
            margin: 20px 0 10px;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            border-left: 4px solid #1a3c6d;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #1a3c6d;
            color: #fff;
            text-align: left;
            padding: 10px;
            font-weight: 600;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            color: #000000;
        }
        tr:nth-child(even) {
            background: #f7fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #e0e8f0;
        }
        .no-data {
            color: #718096;
            font-style: italic;
            padding: 15px;
            text-align: center;
        }
        .payment-history {
            background: #fafafa;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            width: 50%;
            float: right;
        }
        .actions {
            margin-top: 25px;
            text-align: right;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
                font-size: 11px;
            }
            .report-container {
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .payment-history {
                width: 50%;
                float: right;
            }
        }
    </style>

    <div class="report-container">
        <div class="header">
            <h1>www.tariahealth.com</h1>
            <p>Individual Wellness Report | Generated: {{ $currentDate }}</p>
        </div>

        <div class="borrower-info">
            <h2 style="margin-top: 0; font-size: 18px;">Patient Information</h2>
            <table>
                <tr>
                    <td width="20%"><strong>Name:</strong></td>
                    <td width="30%">{{ $registration->first_name }} {{ $registration->surname }}</td>
                    <td width="20%"><strong>Contact:</strong></td>
                    <td>{{ $registration->phone }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $registration->email }}</td>
                    <td><strong>Age:</strong></td>
                    <td>{{ $registration->age }}</td>
                </tr>
            </table>
        </div>

        <div class="section-title">Vital Signs</div>
        <table>
            <tr>
                <th>Measurement</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Blood Pressure</td>
                <td>{{ $vital ? $vital->bp_systolic . '/' . $vital->bp_diastolic . ' mmHg' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Pulse</td>
                <td>{{ $vital ? $vital->pulse . ' bpm' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Temperature</td>
                <td>{{ $vital ? $vital->temp . ' °C' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Blood Sugar</td>
                <td>{{ $vital ? $vital->rbs . ' mg/dL' : 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Nutrition Assessment</div>
        <table>
            <tr>
                <th>Measurement</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Weight</td>
                <td>{{ $nutrition ? $nutrition->weight . ' kgs' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Height</td>
                <td>{{ $nutrition ? $nutrition->height . ' cm' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>BMI</td>
                <td>{{ $nutrition ? $nutrition->bmi : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Body Fat Percentage</td>
                <td>{{ $nutrition ? $nutrition->body_fat_percent . '%' : 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Screening Results</div>
        <div class="no-data">[Screening data to be implemented]</div>

        <div class="section-title">Discussion Summary</div>
        <p>{{ $nutrition ? $nutrition->notes_nutritionist : 'No notes available' }}</p>

        <div class="section-title">Personalized Health Goal</div>
        <div>
            <p>Steady daily movement to increase the level of physical activity</p>
            <p>Aim to lose {{ $nutrition ? round($nutrition->weight - 10, 1) . ' kgs' : '10 kgs' }} over the next 12 months and maintain the habit change to sustain a healthy body weight</p>
            <p>Walk briskly for at least 45 minutes to one hour every other day</p>
            <p>Gradually increase the amount of vegetables in each meal to feel fuller and reduce your carbohydrate intake</p>
            <p><strong>Target:</strong> Goal weight in Sep ’26 is {{ $nutrition ? round($nutrition->weight - 10, 1) : 'N/A' }} kgs</p>
            <p><strong>Healthy weight for height range (kgs):</strong> {{ $nutrition ? $nutrition->lower_limit_weight . ' kgs - ' . $nutrition->weight_limit_weight . ' kgs' : 'N/A' }}</p>
            <p><strong>Healthy Body Fat % in Men 18-24%:</strong> [To be adjusted based on gender/age]</p>
        </div>

        <div class="summary">
            <p><strong>Dr. Mymoona Mohammed</strong></p>
            <p>{{ $currentDate }}</p>
        </div>
    </div>
</div>