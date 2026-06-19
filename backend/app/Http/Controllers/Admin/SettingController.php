<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display the settings management view.
     */
    public function index()
    {
        $settings = $this->settingService->all()->keyBy('setting_key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(UpdateSettingsRequest $request)
    {
        $data = $request->validated();

        // Handle boolean fields which are not sent if unchecked
        $booleans = ['ENABLE_AI_INTERVIEW', 'ENABLE_READ_ALOUD', 'ENABLE_TTS', 'ENABLE_STT'];
        foreach ($booleans as $boolKey) {
            $data[$boolKey] = $request->has($boolKey);
        }

        $this->settingService->updateBulk($data);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    /**
     * Test API connection.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $request->validate([
            'target' => 'required|in:fastapi,gemini'
        ]);
 
        $result = $this->settingService->testConnection($request->input('target'));
 
        return response()->json($result);
    }
}
