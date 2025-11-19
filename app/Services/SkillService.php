<?php

namespace App\Services;

use App\Http\Requests\SkillFormRequest;
use App\Models\Skill;

class SkillService
{
    public function index()
    {
        return Skill::with('freelances', 'projects')->get();
    }

    public function store(SkillFormRequest $request)
    {
        return Skill::create($request->validated());
    }

    public function show(string $id)
    {
        return Skill::with('freelances', 'projects')->findOrFail($id);
    }

    public function update(array $request, string $id)
    {
        $skill = $this->show($id);
        $skill->update($request);
        return $skill;
    }

    public function destroy(string $id)
    {
        Skill::destroy($id);
    }
}
