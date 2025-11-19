<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillFormRequest;
use App\Services\SkillService;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    protected $skillService;

    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
    }

    public function index()
    {
        $skills = $this->skillService->index();
        return response()->json($skills, 200);
    }

    public function store(SkillFormRequest $request)
    {
        $skill = $this->skillService->store($request);
        return response()->json($skill, 201);
    }

    public function show(string $id)
    {
        $skill = $this->skillService->show($id);
        return response()->json($skill, 200);
    }

    public function update(SkillFormRequest $request, string $id)
    {
        $skill = $this->skillService->update($request->validated(), $id);
        return response()->json($skill, 200);
    }

    public function destroy(string $id)
    {
        $this->skillService->destroy($id);
        return response('', 204);
    }
}
