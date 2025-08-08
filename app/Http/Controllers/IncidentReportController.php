<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentReportController extends Controller
{
    // Define the allowed statuses
    private array $validStatuses = ['pending', 'resolved', 'in_progress', 'dismissed'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = IncidentReport::latest()->paginate(10);
        return view('admin.incident_reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.incident_reports.create', [
            'statuses' => $this->validStatuses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'reported_by' => 'required|string|max:255',
            'status' => ['required', 'string', Rule::in($this->validStatuses)],
            'incident_time' => 'required|date',
        ]);

        IncidentReport::create($validated);

        return redirect()->route('incident-reports.index')->with('success', 'Incident Report Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IncidentReport $incidentReport)
    {
        return view('admin.incident_reports.show', compact('incidentReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IncidentReport $incidentReport)
    {
        return view('admin.incident_reports.edit', [
            'incidentReport' => $incidentReport,
            'statuses' => $this->validStatuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IncidentReport $incidentReport)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'reported_by' => 'required|string|max:255',
            'status' => ['required', 'string', Rule::in($this->validStatuses)],
            'incident_time' => 'required|date',
        ]);

        $incidentReport->update($validated);

        return redirect()->route('incident-reports.index')->with('success', 'Incident Report Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IncidentReport $incidentReport)
    {
        $incidentReport->delete();

        return redirect()->route('incident-reports.index')->with('success', 'Incident Report Deleted Successfully.');
    }
}
