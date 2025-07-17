let currentPlayerId = null;

function initAchievements() {
    currentPlayerId = parseInt(localStorage.getItem('playerId')) || 0;
    
    if (!currentPlayerId) {
        console.error('Keine gültige Spieler-ID gefunden');
        window.location.href = 'login.html';
        return;
    }

    loadPlayerAchievements();
}

async function loadPlayerAchievements() {
    try {
        const response = await fetch(`php/achievements.php?action=getPlayerAchievements&playerId=${currentPlayerId}`);
        const data = await response.json();

        if (data.success) {
            displayStats(data.stats, data.totalUnlocked, data.totalAchievements);
            displayAchievements(data.achievements);
        } else {
            console.error('Fehler beim Laden der Achievements:', data.message);
            document.getElementById('achievementsList').innerHTML = '<p>Fehler beim Laden der Achievements.</p>';
        }
    } catch (error) {
        console.error('Netzwerkfehler:', error);
        document.getElementById('achievementsList').innerHTML = '<p>Netzwerkfehler beim Laden der Achievements.</p>';
    }
}

function displayStats(stats, unlockedCount, totalCount) {
    document.getElementById('achievementProgress').textContent = `${unlockedCount} / ${totalCount}`;
    document.getElementById('totalQuestions').textContent = stats.totalQuestions || 0;
    document.getElementById('correctAnswers').textContent = stats.correctAnswers || 0;
    
    // Calculate total points from unlocked achievements
    // This would need to be calculated server-side for accuracy
    document.getElementById('totalPoints').textContent = unlockedCount * 10; // Placeholder
}

function displayAchievements(achievements) {
    const container = document.getElementById('achievementsList');
    
    if (achievements.length === 0) {
        container.innerHTML = '<p>Keine Achievements gefunden.</p>';
        return;
    }

    const achievementsHTML = achievements.map(achievement => {
        const isUnlocked = achievement.isUnlocked;
        const progress = achievement.progress;
        
        const progressPercentage = Math.min(100, progress.percentage);
        const progressText = isUnlocked ? 'Abgeschlossen' : `${progress.current} / ${progress.target}`;
        
        const icon = getAchievementIcon(achievement.RequirementType);
        
        return `
            <div class="achievement ${isUnlocked ? 'unlocked' : ''}">
                <div class="achievement-icon">${icon}</div>
                <div class="achievement-content">
                    <h3 class="achievement-name">${achievement.Name}</h3>
                    <p class="achievement-description">${achievement.Description}</p>
                    <div class="achievement-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progressPercentage}%"></div>
                        </div>
                        <span class="progress-text">${progressText}</span>
                    </div>
                    ${isUnlocked && achievement.unlockedAt ? `<small style="color: #6c757d;">Freigeschaltet: ${formatDate(achievement.unlockedAt)}</small>` : ''}
                </div>
                <div class="achievement-points">${achievement.Points} Punkte</div>
            </div>
        `;
    }).join('');

    container.innerHTML = achievementsHTML;
}

function getAchievementIcon(requirementType) {
    const icons = {
        'QUESTIONS_ANSWERED': '❓',
        'CORRECT_ANSWERS': '✅',
        'GAMES_PLAYED': '🎮',
        'MULTIPLAYER_WINS': '👥',
        'STREAK': '🔥',
        'CATEGORY_MASTER': '🎯'
    };
    return icons[requirementType] || '🏆';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('de-DE');
}

// Function to check for new achievements (can be called from other pages)
async function checkForNewAchievements(playerId) {
    try {
        const response = await fetch(`php/achievements.php?action=checkNewAchievements&playerId=${playerId}`);
        const data = await response.json();

        if (data.success && data.newAchievements.length > 0) {
            // Show achievement notifications
            data.newAchievements.forEach(achievement => {
                showAchievementNotification(achievement);
            });
        }
    } catch (error) {
        console.error('Fehler beim Überprüfen neuer Achievements:', error);
    }
}

function showAchievementNotification(achievement) {
    document.getElementById('achievementName').textContent = achievement.Name;
    document.getElementById('achievementDescription').textContent = achievement.Description;
    document.getElementById('achievementPoints').textContent = `+${achievement.Points} Punkte`;
    
    document.getElementById('achievementModal').style.display = 'flex';
}

function closeAchievementModal() {
    document.getElementById('achievementModal').style.display = 'none';
}

// Export function for use in other scripts
window.checkForNewAchievements = checkForNewAchievements;
window.showAchievementNotification = showAchievementNotification;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', initAchievements);