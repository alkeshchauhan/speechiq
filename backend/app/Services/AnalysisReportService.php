<?php

namespace App\Services;

use App\Repositories\Contracts\AnalysisReportRepositoryInterface;
use App\Models\AnalysisReport;
use App\Models\User;
use App\Models\AudioRecording;
use Illuminate\Support\Facades\DB;

class AnalysisReportService extends BaseService
{
    protected AnalysisReportRepositoryInterface $analysisReportRepository;

    public function __construct(AnalysisReportRepositoryInterface $analysisReportRepository)
    {
        parent::__construct($analysisReportRepository);
        $this->analysisReportRepository = $analysisReportRepository;
    }

    /**
     * Compile and generate analysis report details for a user.
     */
    public function generateUserReport(int $userId): AnalysisReport
    {
        // 1. Fetch completed recordings with results
        $recordings = AudioRecording::where('user_id', $userId)
            ->where('status', 'completed')
            ->with(['readAloudResult', 'interviewResult'])
            ->get();

        $readAloudScores = [];
        $interviewScores = [];
        
        $grammarScores = [];
        $vocabularyScores = [];
        $contentScores = [];
        $confidenceScores = [];
        $pronunciationScores = [];
        $fluencyScores = [];

        $progressTimeline = [];

        foreach ($recordings as $recording) {
            $dateFormatted = $recording->created_at->format('Y-m-d H:i');
            
            // Read Aloud result evaluation
            if ($recording->readAloudResult) {
                $res = $recording->readAloudResult;
                $readAloudScores[] = $res->overall_score;
                $pronunciationScores[] = $res->pronunciation_score;
                $fluencyScores[] = $res->fluency_score;

                $progressTimeline[] = [
                    'date' => $dateFormatted,
                    'type' => 'Read Aloud',
                    'score' => $res->overall_score,
                    'label' => $recording->question ? ($recording->question->test ? $recording->question->test->title : 'Reading Task') : 'Reading Task'
                ];
            }

            // AI Interview result evaluation
            if ($recording->interviewResult) {
                $res = $recording->interviewResult;
                $interviewScores[] = $res->overall_score;
                
                $grammarScores[] = $res->grammar_score;
                $vocabularyScores[] = $res->vocabulary_score;
                $contentScores[] = $res->content_score;
                $confidenceScores[] = $res->confidence_score;
                $pronunciationScores[] = $res->pronunciation_score;
                $fluencyScores[] = $res->fluency_score;

                $progressTimeline[] = [
                    'date' => $dateFormatted,
                    'type' => 'AI Interview',
                    'score' => $res->overall_score,
                    'label' => 'Dynamic Conversation Query'
                ];
            }
        }

        // Chronological sort
        usort($progressTimeline, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // 2. Calculations
        $readAloudAvg = count($readAloudScores) > 0 ? (int) round(array_sum($readAloudScores) / count($readAloudScores)) : 0;
        $interviewAvg = count($interviewScores) > 0 ? (int) round(array_sum($interviewScores) / count($interviewScores)) : 0;
        
        $totalTests = count($readAloudScores) + count($interviewScores);

        // Overall performance score is the average of category scores
        $overallScore = 0;
        if (count($readAloudScores) > 0 && count($interviewScores) > 0) {
            $overallScore = (int) round(($readAloudAvg + $interviewAvg) / 2);
        } elseif (count($readAloudScores) > 0) {
            $overallScore = $readAloudAvg;
        } elseif (count($interviewScores) > 0) {
            $overallScore = $interviewAvg;
        }

        // 3. Compile improvement areas suggestions
        $improvementAreas = [];

        // Evaluate grammar (Interview exclusive)
        if (count($grammarScores) > 0) {
            $grammarAvg = array_sum($grammarScores) / count($grammarScores);
            if ($grammarAvg < 70) {
                $improvementAreas[] = [
                    'metric' => 'Grammar & Syntax',
                    'status' => 'critical',
                    'comment' => 'Your sentences frequently contain structural inconsistencies. Focus on tense matching and subject-verb agreements.'
                ];
            } elseif ($grammarAvg < 85) {
                $improvementAreas[] = [
                    'metric' => 'Grammar & Syntax',
                    'status' => 'moderate',
                    'comment' => 'Sentence syntax is decent but simple. Try using more compound sentences and varied vocabulary clauses.'
                ];
            } else {
                $improvementAreas[] = [
                    'metric' => 'Grammar & Syntax',
                    'status' => 'strength',
                    'comment' => 'Excellent grammatical control with clean sentence structures.'
                ];
            }
        }

        // Evaluate vocabulary (Interview exclusive)
        if (count($vocabularyScores) > 0) {
            $vocabularyAvg = array_sum($vocabularyScores) / count($vocabularyScores);
            if ($vocabularyAvg < 70) {
                $improvementAreas[] = [
                    'metric' => 'Vocabulary Richness',
                    'status' => 'critical',
                    'comment' => 'Highly repetitive word choices. Read context-rich blogs and integrate domain-specific keywords into responses.'
                ];
            } elseif ($vocabularyAvg < 85) {
                $improvementAreas[] = [
                    'metric' => 'Vocabulary Richness',
                    'status' => 'moderate',
                    'comment' => 'Good vocabulary base. To stand out, learn key synonyms and transition terms to elevate expressions.'
                ];
            } else {
                $improvementAreas[] = [
                    'metric' => 'Vocabulary Richness',
                    'status' => 'strength',
                    'comment' => 'Rich lexicon with precise expression parameters.'
                ];
            }
        }

        // Evaluate Pronunciation (Both categories)
        if (count($pronunciationScores) > 0) {
            $pronunciationAvg = array_sum($pronunciationScores) / count($pronunciationScores);
            if ($pronunciationAvg < 70) {
                $improvementAreas[] = [
                    'metric' => 'Pronunciation & Accent',
                    'status' => 'critical',
                    'comment' => 'Several phonetic misinterpretations. Practice syllable stress exercises and verify voice articulation pacing.'
                ];
            } elseif ($pronunciationAvg < 85) {
                $improvementAreas[] = [
                    'metric' => 'Pronunciation & Accent',
                    'status' => 'moderate',
                    'comment' => 'Intonation patterns are clean. Work on rounding out consonant end-sounds clearly.'
                ];
            } else {
                $improvementAreas[] = [
                    'metric' => 'Pronunciation & Accent',
                    'status' => 'strength',
                    'comment' => 'Highly accurate vowel phonetics and accent clarity.'
                ];
            }
        }

        // Evaluate Fluency (Both categories)
        if (count($fluencyScores) > 0) {
            $fluencyAvg = array_sum($fluencyScores) / count($fluencyScores);
            if ($fluencyAvg < 70) {
                $improvementAreas[] = [
                    'metric' => 'Fluency & Tempo',
                    'status' => 'critical',
                    'comment' => 'Excessive pauses and filler words detected. Reduce speaking speed slightly to give yourself time to formulate queries.'
                ];
            } elseif ($fluencyAvg < 85) {
                $improvementAreas[] = [
                    'metric' => 'Fluency & Tempo',
                    'status' => 'moderate',
                    'comment' => 'Pacing is stable. Practice reduction of silent gaps and filler phrases like "um" and "you know".'
                ];
            } else {
                $improvementAreas[] = [
                    'metric' => 'Fluency & Tempo',
                    'status' => 'strength',
                    'comment' => 'Consistent voice modulation and fluent transition execution.'
                ];
            }
        }

        // 4. Update or Create database record
        $report = AnalysisReport::updateOrCreate(
            ['user_id' => $userId],
            [
                'overall_score' => $overallScore,
                'read_aloud_average' => $readAloudAvg,
                'interview_average' => $interviewAvg,
                'total_tests_taken' => $totalTests,
                'progress_data' => $progressTimeline,
                'improvement_areas' => $improvementAreas,
                'pdf_path' => null // Generated on demand or print view print-to-file
            ]
        );

        return $report;
    }
}
