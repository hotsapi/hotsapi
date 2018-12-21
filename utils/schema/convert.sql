-- Convert Replays

with
  scores as (
    select
      int64_field_0 as player_id,
      array_agg(struct(
        int64_field_1 as level,
        int64_field_2 as kills,
        int64_field_3 as assists,
        int64_field_4 as takedowns,
        int64_field_5 as deaths,
        int64_field_6 as highest_kill_streak,
        int64_field_7 as hero_damage,
        int64_field_8 as siege_damage,
        int64_field_9 as structure_damage,
        int64_field_10 as minion_damage,
        int64_field_11 as creep_damage,
        int64_field_12 as summon_damage,
        int64_field_13 as time_cc_enemy_heroes,
        cast(string_field_14 as int64) as healing,
        int64_field_15 as self_healing,
        cast(string_field_16 as int64) as damage_taken,
        int64_field_17 as experience_contribution,
        int64_field_18 as town_kills,
        int64_field_19 as time_spent_dead,
        int64_field_20 as merc_camp_captures,
        int64_field_21 as watch_tower_captures,
        int64_field_22 as meta_experience
      )) as score
    from source.scores
    group by player_id
  ),

  talents as (
    select
      p.int64_field_0 as player_id,
      struct(
        max(if(p.int64_field_2 = 1,  t.string_field_1, null)) as _1,
        max(if(p.int64_field_2 = 4,  t.string_field_1, null)) as _4,
        max(if(p.int64_field_2 = 7,  t.string_field_1, null)) as _7,
        max(if(p.int64_field_2 = 10, t.string_field_1, null)) as _10,
        max(if(p.int64_field_2 = 13, t.string_field_1, null)) as _13,
        max(if(p.int64_field_2 = 16, t.string_field_1, null)) as _16,
        max(if(p.int64_field_2 = 20, t.string_field_1, null)) as _20
      ) as talents
    from source.player_talent p
    left join source.talents t
      on p.int64_field_1 = t.int64_field_0
    group by player_id
  ),

  bans as (
    select
      b.int64_field_1 as replay_id,
      array_agg(struct(
        h.string_field_1 as hero,
        b.int64_field_4 as team,
        b.int64_field_5 as index
      )) as bans
    from source.bans b
    left join source.heroes h
      on cast(b.string_field_2 as int64) = h.int64_field_0
    group by replay_id
  ),

  players as (
    select
      p.int64_field_1 as replay_id,
      array_agg(struct(
        h.string_field_1 as hero,
        p.string_field_3 as battletag_name,
        p.int64_field_4 as battletag_id,
        p.int64_field_5 as hero_level,
        p.int64_field_6 as team,
        cast(p.int64_field_7 as bool) as winner,
        p.int64_field_8 as blizz_id,
        p.int64_field_9 as party,
        cast(p.int64_field_10 as bool) as silenced,
        t.talents,
        s.score
      )) as players
    from source.players p
    left join talents t
      on t.player_id = p.int64_field_0
    left join scores s
      on s.player_id = p.int64_field_0
    left join source.heroes h
      on p.int64_field_2 = h.int64_field_0
    group by replay_id
  ),

  replays as (
    select
      r.int64_field_0 as id,
      r.int64_field_1 as parsed_id,
      r.timestamp_field_2 as created_at,
      r.timestamp_field_3 as updated_at,
      r.string_field_4 as filename,
      r.int64_field_5 as size,
      r.string_field_6 as game_type,
      r.timestamp_field_7 as game_date,
      r.int64_field_8 as game_length,
      m.string_field_1 as game_map,
      r.string_field_10 as game_version,
      r.int64_field_11 as region,
      r.string_field_12 as fingerprint,
      b.bans,
      p.players
    from source.replays r
    left join bans b
      on b.replay_id = r.int64_field_0
    left join players p
      on p.replay_id = r.int64_field_0
    left join source.maps m
      on r.int64_field_9 = m.int64_field_0
  )

select * from replays;

-- Convert Heroes

with
  abilities as (
    select int64_field_1 as hero_id, array_agg(struct(
          string_field_2 as owner,
          string_field_3 as name,
          string_field_4 as title,
          string_field_5 as description,
          string_field_6 as icon,
          string_field_7 as hotkey,
          cast(string_field_8 as int64) as cooldown,
          cast(string_field_9 as int64) as mana_cost,
          cast(int64_field_10 as bool) as trait
        )) as abilities
      from source.abilities
      group by hero_id
  ),

  talents as (
    select ts.int64_field_0 as hero_id, array_agg(struct(
          t.string_field_1 as name,
          t.string_field_2 as title,
          t.string_field_3 as description,
          t.string_field_4 as icon,
          cast(t.string_field_5 as int64) as level,
          t.string_field_6 as ability,
          cast(t.string_field_7 as int64) as sort,
          cast(t.string_field_8 as int64) as cooldown,
          cast(t.string_field_9 as int64) as mana_cost
        )) as talents
      from source.hero_talent ts
      left join source.talents t
      on t.int64_field_0 = ts.int64_field_1
      group by hero_id
  ),

  heroes as (
    select int64_field_0 as id,
           string_field_1 as name,
           string_field_2 as short_name,
           string_field_3 as role,
           string_field_4 as type,
           date_field_5 as release_date,
           string_field_6 as attribute_id
    from source.heroes
  )

select h.name, h.short_name, h.role, h.type, h.release_date, h.attribute_id, a.abilities, t.talents
  from heroes h
  left join abilities a on h.id = a.hero_id
  left join talents t on h.id = t.hero_id;

-- Convert maps

with
  translations as (
    select
      int64_field_1 as map_id,
      array_agg(string_field_2) as translations
    from source.map_translations
    group by map_id
  )

select
  m.string_field_1 as name,
  tr.translations
from source.maps m
left join transactions tr
  on tr.map_id = m.int64_field_0;
