INSERT INTO questions (
    category,
    difficulty,
    age_min,
    age_max,
    question_type,
    question_text,
    correct_answer,
    answer_options_json,
    nearest_value,
    is_ai_generated,
    is_approved,
    language
) VALUES
('Allmänt', 'easy', 7, 120, 'multiple_choice', 'Vilken färg får du om du blandar blått och gult?', 'Grön', '["Grön","Röd","Lila","Orange"]', NULL, 0, 1, 'sv'),
('Geografi', 'easy', 7, 120, 'multiple_choice', 'Vad är huvudstaden i Sverige?', 'Stockholm', '["Stockholm","Göteborg","Malmö","Uppsala"]', NULL, 0, 1, 'sv'),
('Film & TV', 'easy', 7, 120, 'multiple_choice', 'Vad heter lejonungen i Lejonkungen?', 'Simba', '["Simba","Mufasa","Timon","Scar"]', NULL, 0, 1, 'sv'),
('Vetenskap', 'medium', 10, 120, 'multiple_choice', 'Vilken planet är närmast solen?', 'Merkurius', '["Mars","Venus","Merkurius","Jorden"]', NULL, 0, 1, 'sv'),
('Musik', 'easy', 7, 120, 'multiple_choice', 'Hur många strängar har en vanlig gitarr?', '6', '["4","5","6","7"]', NULL, 0, 1, 'sv'),
('Historia', 'medium', 12, 120, 'multiple_choice', 'Vilket år började andra världskriget?', '1939', '["1918","1939","1945","1961"]', NULL, 0, 1, 'sv');