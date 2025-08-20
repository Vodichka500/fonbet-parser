import React, {useMemo, useState} from 'react';

export default function ({ matches, logs }) {
    const [searchTerm, setSearchTerm] = useState("")
    const [selectedTournament, setSelectedTournament] = useState("")
    const [selectedDiscipline, setSelectedDiscipline] = useState("")
    const [sortOrder, setSortOrder] = useState("desc")
    const [activeTab, setActiveTab] = useState("matches")
    const [localLogs, setLocalLogs] = useState(logs)

    // Get unique tournaments and disciplines for filters
    const tournaments = useMemo(() => {
        const unique = Array.from(new Set(matches.map((match) => match.tournament.name)))
        return unique.sort()
    }, [matches])

    const disciplines = useMemo(() => {
        const unique = Array.from(new Set(matches.map((match) => match.discipline)))
        return unique.sort()
    }, [matches])

    // Filter and sort matches
    const filteredAndSortedMatches = useMemo(() => {
        const filtered = matches.filter((match) => {
            const matchesSearch =
                searchTerm === "" ||
                match.team1.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                match.team2.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                match.tournament.name.toLowerCase().includes(searchTerm.toLowerCase())

            const matchesTournament = selectedTournament === "" || match.tournament.name === selectedTournament
            const matchesDiscipline = selectedDiscipline === "" || match.discipline === selectedDiscipline

            return matchesSearch && matchesTournament && matchesDiscipline
        })

        // Sort by match ID as a proxy for date (assuming higher ID = more recent)
        filtered.sort((a, b) => {
            const dateA = new Date(a.match_date);
            const dateB = new Date(b.match_date);
            return sortOrder === "desc" ? dateB - dateA : dateA - dateB;
        });

        return filtered
    }, [matches, searchTerm, selectedTournament, selectedDiscipline, sortOrder])

    const clearFilters = () => {
        setSearchTerm("")
        setSelectedTournament("")
        setSelectedDiscipline("")
    }

    const clearLogs = async () => {
        try {
            const response = await fetch("/logs/clear", {
                method: "POST",
            });

            if (response.ok) {
                setLocalLogs([]);
            } else {
                console.error("Error with clear logs");
            }
        } catch (err) {
            console.error("Network error:", err);
        }
    };

    return (
        <div className="matches-container">
            <div className="header">
                <h1>Match Results</h1>
            </div>

            <div className="tabs">
                <button className={`tab ${activeTab === "matches" ? "active" : ""}`} onClick={() => setActiveTab("matches")}>
                    Matches ({matches.length})
                </button>
                <button className={`tab ${activeTab === "logs" ? "active" : ""}`} onClick={() => setActiveTab("logs")}>
                    Logs
                </button>
            </div>

            {activeTab === "matches" ? (
                <>
                    <div className="controls">
                        <input
                            type="text"
                            className="search-bar"
                            placeholder="Search by team name or tournament..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />

                        <div className="filters">
                            <div className="filter-group">
                                <label className="filter-label">Tournament</label>
                                <select
                                    className="filter-select"
                                    value={selectedTournament}
                                    onChange={(e) => setSelectedTournament(e.target.value)}
                                >
                                    <option value="">All Tournaments</option>
                                    {tournaments.map((tournament) => (
                                        <option key={tournament} value={tournament}>
                                            {tournament}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="filter-group">
                                <label className="filter-label">Sport/Discipline</label>
                                <select
                                    className="filter-select"
                                    value={selectedDiscipline}
                                    onChange={(e) => setSelectedDiscipline(e.target.value)}
                                >
                                    <option value="">All Sports</option>
                                    {disciplines.map((discipline) => (
                                        <option key={discipline} value={discipline}>
                                            {discipline}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div className="controls-footer">
                            <div className="sort-controls">
                                <span className="filter-label">Sort by date:</span>
                                <button
                                    className={`sort-button ${sortOrder === "desc" ? "active" : ""}`}
                                    onClick={() => setSortOrder("desc")}
                                >
                                    Newest First
                                </button>
                                <button
                                    className={`sort-button ${sortOrder === "asc" ? "active" : ""}`}
                                    onClick={() => setSortOrder("asc")}
                                >
                                    Oldest First
                                </button>
                            </div>

                            <div>
                <span className="results-count">
                  {filteredAndSortedMatches.length} of {matches.length} matches
                </span>
                                {(searchTerm || selectedTournament || selectedDiscipline) && (
                                    <button className="clear-button" onClick={clearFilters}>
                                        Clear Filters
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="matches-grid">
                        {filteredAndSortedMatches.length > 0 ? (
                            filteredAndSortedMatches.map((match) => (
                                <div key={match.id} className="match-card">
                                    <div className="match-header">
                                        <div className="tournament-info">
                                            <h3 className="tournament-name">{match.tournament.name}</h3>
                                            <div className="match-meta">
                                                {match.discipline} • {match.match_format.toUpperCase()} •{" "}
                                                {new Date(match.match_date).toLocaleString()}
                                            </div>
                                        </div>
                                        <div className={`status ${match.status.toLowerCase()}`}>{match.status}</div>
                                    </div>

                                    <div className="teams">
                                        <div className="team">
                                            <div className="team-name">{match.team1.name}</div>
                                            <div className="team-score">{match.score1}</div>
                                        </div>
                                        <div className="vs">VS</div>
                                        <div className="team">
                                            <div className="team-name">{match.team2.name}</div>
                                            <div className="team-score">{match.score2}</div>
                                        </div>
                                    </div>

                                    {match.subMatches && match.subMatches.length > 0 && (
                                        <div className="submatches">
                                            <div className="submatches-title">Submatches ({match.subMatches.length})</div>
                                            <div className="submatch-list">
                                                {match.subMatches.map((submatch) => (
                                                    <div key={submatch.id} className="submatch">
                                                        {submatch.score1} - {submatch.score2}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="empty-state">
                                <h3>No matches found</h3>
                                <p>Try adjusting your search or filter criteria</p>
                            </div>
                        )}
                    </div>
                </>
            ) : (
                <div className="logs-section">
                    <div className="logs-header">
                        <h2 className="logs-title">Parser Logs</h2>
                        <button className="clear-logs-button" onClick={clearLogs}>✕ Clear</button>
                    </div>
                    <div className="logs-content">
                        {localLogs && localLogs.length > 0 ? localLogs.join("\n") : "No logs available"}
                    </div>
                </div>
            )}
        </div>
    )
}
