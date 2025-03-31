# Quizzgame
Quizzgame für Lernfeld 8 - Gruppe 15

Smart Ziel: Unsere Gruppe hat das Ziel bis zum 08.04.2025 eine fertige funktionierende Quiz Website aufzubauen, auf welcher Nutzer qualifiziert ihr Wissen testen können.


S = Das  Ziel ist spezifisch benannt: Eine fertige funktionierende Website


M = Das Ergebnis ist messbar: Funktionierende Website und Nutzer können die Website fehlerfrei nutzen


A = Das Ziel ist erreichbar: Die Website kann in dieser Zeit funkionell und fehlerfrei aufgebaut werden


R = Das Ziel ist realisitisch: Die Website sollte in dieser Zeit aufbaubar sein


T = Die Ergebnis-Zeit ist benannt: Bis zum 08.04.2025

# UseCase Diagramm

```mermaid
%% Use-Case Diagramm für Quizgame
%% Teilnehmer: Spieler, Admin

actor Spieler
actor Admin

rectangle QuizGame {
  Spieler --> (Einloggen)
  Spieler --> (Registrieren)
  Spieler --> (Frage beantworten)
  Spieler --> (Neue Frage laden)
  Spieler --> (Spielstand speichern)
  Spieler --> (Punkte einsehen)

  Admin --> (Einloggen)
  Admin --> (Neue Frage hinzufügen)
  Admin --> (Fragenliste verwalten)
  Admin --> (Spielstatistiken ansehen)
}
