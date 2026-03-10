<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alumni Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Inter', sans-serif; background: #ffffff; color: #111827; padding: 60px 24px 80px; }
  .page-title { font-size: 2rem; font-weight: 800; text-align: center; color: #111827; margin-bottom: 40px; }
  .section { margin-bottom: 48px; }
  .section-heading { font-size: .95rem; font-weight: 700; color: #4a6fa5; margin-bottom: 12px; }
  .centered { max-width: 780px; margin: 0 auto; }
  .form-wrap { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; }
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .span2 { grid-column: 1/-1; }
  .field { display: flex; flex-direction: column; gap: 5px; }
  .field label { font-size: .75em; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
  .field input, .field select, .field textarea { padding: 9px 12px; border: 1.5px solid #d1d5db; border-radius: 6px; font-size: .9em; font-family: inherit; color: #111827; background: #fff; transition: border-color .15s; }
  .field input:focus, .field select:focus, .field textarea:focus { outline: none; border-color: #3b5bdb; }
  .field textarea { height: 76px; resize: vertical; }
  .form-actions { margin-top: 14px; }
  .table-wrap { max-width: 780px; margin: 0 auto; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
  table { width: 100%; border-collapse: collapse; font-size: .93rem; }
  thead th { background: #1e2a4a; color: #fff; padding: 12px 18px; text-align: left; font-weight: 600; font-size: .88rem; }
  tbody td { padding: 13px 18px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { background: #f9fafb; }
  .td-act { display: flex; gap: 8px; align-items: center; }
  .empty-row td { text-align: center; color: #9ca3af; font-style: italic; padding: 22px; }
  .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 24px; border-radius: 8px; font-size: .9rem; font-weight: 600; font-family: inherit; cursor: pointer; border: none; text-decoration: none; transition: opacity .15s; }
  .btn:hover { opacity: .88; }
  .btn-blue  { background: #3b5bdb; color: #fff; }
  .btn-gray  { background: #6b7280; color: #fff; }
  .btn-red   { background: #ef4444; color: #fff; }
  .btn-ghost { background: #e5e7eb; color: #374151; }
  .btn-sm { padding: 5px 14px; font-size: .8rem; border-radius: 6px; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: .78em; font-weight: 700; }
  .badge-active  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
  .badge-blue    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .badge-gray    { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }
  .inline-assign { display: flex; gap: 6px; align-items: center; }
  .inline-assign input { padding: 5px 9px; border: 1.5px solid #d1d5db; border-radius: 6px; font-size: .83em; width: 130px; font-family: inherit; }
  .bottom-actions { display: flex; justify-content: center; gap: 14px; margin-top: 12px; }
</style>
</head>
<body>

<div class="page-title">Alumni & Faculty Portal</div>

<!-- ── EVENTS ── -->
<div class="section">
  <div class="centered">
    <div class="section-heading">Post an Event</div>
    <div class="form-wrap">
      <div class="form-grid">
        <div class="field"><label>Event Title</label><input type="text" name="title"></div>
        <div class="field"><label>Event Date</label><input type="date" name="event_date"></div>
        <div class="field"><label>Location</label><input type="text" name="location"></div>
        <div class="field"><label>Posted By</label><input type="text" name="posted_by"></div>
        <div class="field span2"><label>Description</label><textarea name="description"></textarea></div>
      </div>
      <div class="form-actions"><button class="btn btn-blue">Post Event</button></div>
    </div>
    <div class="section-heading" style="margin-top:24px;">Upcoming Events</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Event ID</th><th>Title</th><th>Date</th><th>Location</th><th>Posted By</th><th>Actions</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="6">No events posted yet</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ── JOBS ── -->
<div class="section">
  <div class="centered">
    <div class="section-heading">Post a Job</div>
    <div class="form-wrap">
      <div class="form-grid">
        <div class="field"><label>Job Title</label><input type="text" name="title"></div>
        <div class="field"><label>Company</label><input type="text" name="company"></div>
        <div class="field"><label>Location</label><input type="text" name="location"></div>
        <div class="field"><label>Job Type</label>
          <select name="job_type"><option>Full-Time</option><option>Part-Time</option><option>Internship</option><option>Contract</option></select>
        </div>
        <div class="field"><label>Posted By</label><input type="text" name="posted_by"></div>
        <div class="field span2"><label>Description</label><textarea name="description"></textarea></div>
      </div>
      <div class="form-actions"><button class="btn btn-blue">Post Job</button></div>
    </div>
    <div class="section-heading" style="margin-top:24px;">Current Job Postings</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Job ID</th><th>Title</th><th>Company</th><th>Location</th><th>Type</th><th>Action</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="6">No jobs posted yet</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ── MENTORSHIPS ── -->
<div class="section">
  <div class="centered">
    <div class="section-heading">Add a Mentor</div>
    <div class="form-wrap">
      <div class="form-grid">
        <div class="field"><label>Mentor Name</label><input type="text" name="mentor_name"></div>
        <div class="field"><label>Mentor Email</label><input type="email" name="mentor_email"></div>
        <div class="field span2"><label>Industry / Field</label><input type="text" name="industry"></div>
      </div>
      <div class="form-actions"><button class="btn btn-blue">Add Mentor</button></div>
    </div>
    <div class="section-heading" style="margin-top:24px;">Mentorship List</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Mentor</th><th>Email</th><th>Industry</th><th>Mentee</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <tr class="empty-row"><td colspan="6">No mentors added yet</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="bottom-actions">
  <a href="preview_student.html" class="btn btn-blue">Student View</a>
  <a href="#" class="btn btn-gray">Back to Home Page</a>
</div>

</body>
</html>
